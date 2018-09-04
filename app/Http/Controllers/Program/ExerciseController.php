<?php

namespace App\Http\Controllers\Program;

use App\Exceptions\ExerciseException;
use App\Http\Controllers\Controller;
use App\Http\Filters\ExerciseFilter;
use App\Models\Prescription\ExerciseMode;
use App\Models\Prescription\ExerciseTemplate;
use App\Models\Prescription\ExerciseType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

/**
 * controller to manage exercise template operations
 *
 * Class ExerciseController
 * @package App\Http\Controllers\Program
 */
class ExerciseController extends Controller
{
    //
    public function __construct(Request $request){
    	parent::__construct($request);
    }

    /**
     * query exercise templates with filter
     *
     * @param Request $request
     * @return mixed
     */
    public function templates(Request $request) {
    	$filter = $this->createExerciseFilter($request);
    	$result = ExerciseTemplate::search($filter);
    	return $this->success('exercise.template.loaded', $result);
    }

    /**
     * create one exercise template
     *
     * @param Request $request
     * @return mixed
     */
    public function createTemplate(Request $request) {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name'=> 'required',
            'exercise_mode_id'=>'required|integer',
            'exercise_type_id'=>'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->error(Response::HTTP_BAD_REQUEST, "exercise.template.invalid_details", $validator->errors());
        }
        $tmpls = null;
        try{
            $tmpls = ExerciseTemplate::createOne($request->all());
        }catch(ExerciseException $e) {
            report($e);
            return $this->error(Response::HTTP_BAD_REQUEST, $e->getMessage(), $data);
        }
        return $this->success('exercise.template.created', ['id'=>$tmpls->id]);

    }

    /**
     * update one exercise template
     *
     * @param Request $request
     * @param ExerciseTemplate $exerciseTemplate
     * @return mixed
     */
    public function updateTemplate(Request $request, ExerciseTemplate $exerciseTemplate) {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name'=> 'required',
            'exercise_mode_id'=>'required|integer',
            'exercise_type_id'=>'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->error(Response::HTTP_BAD_REQUEST, "exercise.template.invalid_details", $validator->errors());
        }
        try{
            $exerciseTemplate->updateIt($data);
        }catch(ExerciseException $e) {
            report($e);
            return $this->error(Response::HTTP_BAD_REQUEST, $e->getMessage(), $data);
        }
        return $this->success('exercise.template.updated', ['id'=>$exerciseTemplate->id]);

    }

    /**
     * load one exercise template
     *
     * @param Request $request
     * @param ExerciseTemplate $exerciseTemplate
     * @return mixed
     */
    public function getTemplate(Request $request, ExerciseTemplate $exerciseTemplate) {
        $exerciseTemplate->load('type.prescriptionAttributes');
        return $this->success('exercise.template.loaded', $exerciseTemplate);
    }

    /**
     * delete the given exercise template
     *
     * @param Request $request
     * @param ExerciseTemplate $exerciseTemplate
     * @return mixed
     */
    public function deleteTemplate(Request $request, ExerciseTemplate $exerciseTemplate) {
        if(!$exerciseTemplate) {
            return $this->error(Response::HTTP_NOT_FOUND, 'exercise.template.not_found', null);
        }
        try{
            $exerciseTemplate->deleteIt();
        }catch(ExerciseException $e) {
            report($e);
            return $this->error(Response::HTTP_BAD_REQUEST, $e->getMessage(), ['id'=>$exerciseTemplate->id]);
        }
        return $this->success('exercise.template.deleted', ['id'=>$exerciseTemplate->id]);
    }

    public function modes(Request $request) {
        return $this->success('exercise.mode.loaded', ExerciseMode::all());
    }

    public function types(Request $request) {
        return $this->success('exercise.type.loaded', ExerciseType::all());
    }

    public function typeAttributes(Request $request, ExerciseType $exerciseType) {
        $exerciseType->load('prescriptionAttributes.units');
        return $this->success('exercise.attribute.loaded', $exerciseType->prescriptionAttributes->each(function (&$attr) {
            foreach($attr->units as &$unit) {
                $unit->specification = json_decode($unit->specification);
            }
        }));
    }

    private function createExerciseFilter(Request $request) {
    	$filter_data = $request->get('filter', []);
    	return new ExerciseFilter($filter_data);
    }
}
