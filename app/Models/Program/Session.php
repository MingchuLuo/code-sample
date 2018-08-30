<?php

namespace App\Models\Program;

use App\Enums\SessionType;
use App\Exceptions\ProgramException;
use App\Models\Prescription\ExerciseTemplate;
use App\Models\Questionnaire\Question;
use App\Models\Questionnaire\Questionnaire;
use App\Models\Questionnaire\Topic;
use App\Models\Testing\TestTemplate;
use App\Models\Testing\Test;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Session extends Model
{

    use SoftDeletes;

    protected $fillable = ['name', 'description', 'day', 'stage_id', 'program_id', 'type', 'layout', 'equipments', 'image_path', 'purpose'];

    protected $dates = ['deleted_at'];

    public function stage() {
        return $this->belongsTo(Stage::class, 'stage_id');
    }

    public function program() {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function exercises() {
        return $this->hasMany(Exercise::class, 'session_id');
    }

    /**
     * relationship to Test
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tests() {
        return $this->hasMany(Test::class, 'session_id');
    }


    public function questionnaires() {
        return $this->hasMany(Questionnaire::class, 'session_id');
    }

    public static function createOne(Stage $stage, $data=array()) {

        $type = ensure($data['type'], '');
        $layout = ensure($data['layout'], new \stdClass());


        $sanitised = [
            'type' => SessionType::validate($type, SessionType::TRAINING),
            'layout' => json_encode($layout),
            'program_id' => $stage->program->id
        ];

        $session = parent::create(array_merge($data, $sanitised));

        $stage->sessions()->save($session);

        return $session;
    }

    public function updateIt($data=array()) {

        $type = ensure($data['type'], '');
        $layout = ensure($data['layout'], new \stdClass());
        $realType = SessionType::validate($type);
        if(!$realType || empty($realType)) {
            throw new ProgramException('program.session.invalid_type');
        }

        $sanitised = [
            'type' => $realType,
            'layout' => json_encode($layout),
        ];

        return parent::update(array_merge($data, $sanitised));
    }


    public function addExercise($data=array()) {
        $exercise_template_id = ensure($data['exercise_template_id'], 0);
        try{
            $tmpl = ExerciseTemplate::findOrFail($exercise_template_id);
        }catch (Exception $e) {
            report($e);
            throw new ProgramException('exercise.template.not_found', $e);
        }
        $exercise = Exercise::createFromTemplate($tmpl, $data);
        $this->exercises()->save($exercise);
        $this->updateEquipments();
        return $exercise;
    }

    public function deleteExercise(Exercise $exercise) {
        $result = $exercise->delete();
        $this->updateEquipments();
        return $result;
    }

    /**
     * @param array $data
     * @return Test
     * @throws ProgramException
     */
    public function addTesting($data=array()) {

        $test_type_id = ensure($data['test_type_id'], 0);
        $name = ensure($data['name'], 0);

        try {
            $testing = Test::createOne([
                'name' => $name,
                'test_type_id' => $test_type_id,
                'session_id' => $this->id
            ]);
        } catch (Exception $e) {
            report($e);
            throw new ProgramException('program.test.cannot_create', $e);
        }

        $this->tests()->save($testing);
        return $testing;
    }

    public function addQuestionnaire(Topic $topic) {
        return Questionnaire::create([
            'session_id' => $this->id,
            'topic_id' => $topic->id
        ]);
    }

    private function updateEquipments()
    {
        $equipments = null;
        switch ($this->type) {
            case SessionType::TRAINING : {
                $equipments = $this->exercises->reduce(function ($carry, $exercise) {
                    $carry = $carry->merge(explode(',', $exercise->template->equipments));
                    return $carry;
                }, collect([]))->reject(function($item){return empty($item);})->unique();
                break;
            }
            case SessionType::TESTING : {
                $equipments = $this->tests->reduce(function ($carry, $test) {
                    $carry = $carry->merge(explode(',', $test->exerciseTemplate->equipments));
                    return $carry;
                }, collect([]))->reject(function($item){return empty($item);})->unique();
                break;
            }
        }

        $this->update(['equipments' => ','. $equipments->implode(',') .',']);

        $this->program->updateEquipments();
    }

    public function getLayoutAttribute($layout) {
        return json_decode($layout);
    }
}
