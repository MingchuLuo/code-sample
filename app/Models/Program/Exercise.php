<?php

namespace App\Models\Program;

use App\Models\Prescription\ExerciseTemplate;
use App\Models\Prescription\PrescriptionAttribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Exercise extends Model
{
    use SoftDeletes;

    //
    protected $fillable = ['session_id', 'exercise_template_id','name', 'run_mode'];

    protected $dates = ['deleted_at'];

    public function session() {
        return $this->belongsTo(Session::class, 'session_id');
    }

    public function template() {
        return $this->belongsTo(ExerciseTemplate::class, 'exercise_template_id');
    }

    public function attributes() {
        return $this->hasMany(ExercisePrescription::class, 'exercise_id');
    }

    public static function createFromTemplate(ExerciseTemplate $tmpl, $data=array()) {
        $content = [
            'exercise_template_id' => $tmpl->id,
            'name' => $tmpl->name,
        ];

        return new Exercise(array_merge($content, $data));
    }

    public function updateIt($data=array()) {
        $attrs = ensure($data['attributes'], []);
        $run_mode = ensure($data['run_mode'], '');

        foreach($attrs as $attr) {
            ExercisePrescription::find($attr['id'])->update($attr);
        }

        return parent::update(['run_mode'=>$run_mode]);
    }

    public function addAttributes($attr_ids=array()) {
        if(count($attr_ids)==0) {
            return false;
        }
        $exercisePrescriptions = [];
        foreach($attr_ids as $attr_id) {
            $exercisePrescriptions[] = ExercisePrescription::create(['prescription_attribute_id'=>$attr_id]);
        }
        $this->attributes()->saveMany($exercisePrescriptions);

        return true;
    }
}
