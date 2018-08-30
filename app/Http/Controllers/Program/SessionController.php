<?php

namespace App\Http\Controllers\Program;

use App\Exceptions\ProgramException;
use App\Models\Prescription\ExerciseTemplate;
use App\Models\Prescription\PrescriptionAttribute;
use App\Models\Program\Exercise;
use App\Models\Program\ExercisePrescription;
use App\Models\Program\Program;
use App\Models\Program\ProgramValidation;
use App\Models\Program\Session;
use App\Models\Program\Stage;
use App\Models\Questionnaire\Questionnaire;
use App\Models\Questionnaire\Topic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class SessionController extends Controller
{

    use ProgramValidation;
    //
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function createSession(Request $request, Program $program, Stage $stage) {
        if(!$this->validateProgramStructure($program, $stage)){
            return $this->error(Response::HTTP_NOT_FOUND, 'program.stage.not_found');
        }
        $data = $request->all();

        $validator = Validator::make($data, [
            'type'=> 'required',
        ]);

        if ($validator->fails()) {
            return $this->error(Response::HTTP_BAD_REQUEST, "program.session.invalid_details", $validator->errors());
        }

        $data['program_id'] = $program->id;
        $session = Session::createOne($stage, $data);

        return $this->success('program.session.created', ['id'=>$session->id]);

    }

    public function getSession(Request $request, Program $program, Stage $stage, Session $session) {
        if(!$this->validateProgramStructure($program, $stage, $session)){
            return $this->error(Response::HTTP_NOT_FOUND, 'program.session.not_found');
        }
        $session->load(['exercises', 'questionnaires', 'tests']);
        $session->exercises->load('attributes');
        $session->tests->load(['template', 'testSets']);
        $session->tests->each(function($test){
            $test->testSets->each(function($testSet){
                $testSet->load(['fields', 'fields.origin']);
            });
        });
        return $this->success('program.session.loaded', $session);
    }

    public function updateSession(Request $request, Program $program, Stage $stage, Session $session) {
        if(!$this->validateProgramStructure($program, $stage, $session)){
            return $this->error(Response::HTTP_NOT_FOUND, 'program.session.not_found');
        }
        try{
            $session->updateIt($request->all());
        }catch(ProgramException $exception) {
            return $this->error(Response::HTTP_BAD_REQUEST, $exception->getMessage());
        }

        return $this->success('program.session.updated', ['id'=>$session->id]);
    }

    public function deleteSession(Request $request, Program $program, Stage $stage, Session $session) {
        if(!$this->validateProgramStructure($program, $stage, $session)){
            return $this->error(Response::HTTP_NOT_FOUND, 'program.session.not_found');
        }
        $session->delete();
        return $this->success('program.session.deleted', ['id'=>$session->id]);
    }

    public function addExercise(Request $request, Program $program, Stage $stage, Session $session) {
        if(!$this->validateProgramStructure($program, $stage, $session)){
            return $this->error(Response::HTTP_NOT_FOUND, 'program.session.not_found');
        }
        $data = $request->all();
        $validator = Validator::make($data, [
            'exercise_template_id'=> 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->error(Response::HTTP_BAD_REQUEST, 'program.session.invalid_exercise_details', $validator->errors());
        }

        try{
            $exercise = $session->addExercise($data);
        }catch (ProgramException $e) {
            return $this->error(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
        return $this->success('program.exercise.created', ['id'=>$exercise->id]);

    }

    public function getExercise(Request $request, Program $program, Stage $stage, Session $session, Exercise $exercise) {
        if(!$this->validateProgramStructure($program, $stage, $session, $exercise)){
            return $this->error(Response::HTTP_NOT_FOUND, 'program.exercise.not_found');
        }
        $exercise->load('attributes');
        return $this->success('program.exercise.loaded', $exercise);
    }

    public function deleteExercise(Request $request, Program $program, Stage $stage, Session $session, Exercise $exercise) {
        if(!$this->validateProgramStructure($program, $stage, $session, $exercise)){
            return $this->error(Response::HTTP_NOT_FOUND, 'program.exercise.not_found');
        }
        $session->deleteExercise($exercise);
        return $this->success('program.exercise.deleted', ['id'=>$exercise->id]);
    }

    public function updateExercise(Request $request, Program $program, Stage $stage, Session $session, Exercise $exercise) {
        if(!$this->validateProgramStructure($program, $stage, $session, $exercise)){
            return $this->error(Response::HTTP_NOT_FOUND, 'program.exercise.not_found');
        }
        try{
            $exercise->updateIt($request->all());
        }catch (ProgramException $e) {
            return $this->error(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
        return $this->success('program.exercise.updated', ['id'=>$exercise->id]);
    }

    public function addAttributes(Request $request, Program $program, Stage $stage, Session $session, Exercise $exercise) {
        if(!$this->validateProgramStructure($program, $stage, $session, $exercise)){
            return $this->error(Response::HTTP_NOT_FOUND,'program.exercise.not_found');
        }
        $attr_ids = $request->get('prescription_attribute_ids', []);
        if(!$exercise->template->validateAttributes($attr_ids)){
            return $this->fail('program.exercise.invalid_attributes', $attr_ids);
        }
        $exercise->addAttributes($attr_ids);
        return $this->success('program.attribute.created', $exercise->attributes->map(function($item) {
            return $item->id;
        }));
    }

    /**
     * @param Request $request
     * @param Program $program
     * @param Stage $stage
     * @param Session $session
     * @param Exercise $exercise
     * @param ExercisePrescription $exercisePrescription
     * @return $this
     */
    public function deleteAttribute(Request $request, Program $program, Stage $stage, Session $session, Exercise $exercise, ExercisePrescription $exercisePrescription)
    {
        if(!$this->validateProgramStructure($program, $stage, $session, $exercise)){
            return $this->error(Response::HTTP_NOT_FOUND, 'program.exercise.not_found');
        }
        $exercisePrescription->delete();
        return $this->success('program.attribute.deleted', ['id'=>$exercisePrescription->id]);
    }

    public function addQuestionnaire(Request $request, Program $program, Stage $stage, Session $session, Topic $topic) {
        if(!$this->validateProgramStructure($program, $stage, $session)){
            return $this->error(Response::HTTP_NOT_FOUND, 'program.topic.not_found');
        }
        $questionnaire = $session->addQuestionnaire($topic);
        return $this->success('program.questionnaire.created', ['id'=> $questionnaire->id]);
    }

    public function deleteQuestionnaire(Request $request, Program $program, Stage $stage, Session $session, Questionnaire $questionnaire) {
        if(!$this->validateProgramStructure($program, $stage, $session, $questionnaire)){
            return $this->error(Response::HTTP_NOT_FOUND, 'program.questionnaire.not_found');
        }
        $questionnaire->deleteIt();
        return $this->success('program.questionnaire.deleted', ['id'=>$questionnaire->id]);
    }

}
