<?php

namespace App\Http\Controllers\Program;

use App\Enums\EntityStatus;
use App\Enums\UserType;
use App\Exceptions\ProgramException;
use App\Http\Controllers\Controller;
use App\Http\Filters\ProgramFilter;
use App\Models\Program\Program;
use App\Models\Program\ProgramCategory;
use App\Models\Program\ProgramValidation;
use App\Models\Program\Stage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * controller to manage program structure operations
 *
 * Class ProgramController
 * @package App\Http\Controllers\Program
 */
class ProgramController extends Controller
{
    use ProgramValidation;
    //
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    /**
     * get a program
     *
     * @param Request $request
     * @param Program $program
     * @return mixed
     */
    public function getProgram(Request $request, Program $program) {
        $program->load(['categories', 'stages.sessions', 'nutritionPlan.sections.files']);
        return $this->success('program.program.loaded', $program);
    }

    /**
     * get all program categories
     *
     * @param Request $request
     * @return mixed
     */
    public function categories(Request $request) {
        return $this->success('program.category.loaded', ProgramCategory::all());
    }

    /**
     * get one program category
     *
     * @param Request $request
     * @param ProgramCategory $programCategory
     * @return mixed
     */
    public function getCategory(Request $request, ProgramCategory $programCategory) {
        return $this->success('program.category.loaded', $programCategory);
    }

    /**
     * create one program category
     *
     * @param Request $request
     * @return mixed
     */
    public function createCategory(Request $request) {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name'=> 'required',
        ]);

        if ($validator->fails()) {
            return $this->error(Response::HTTP_BAD_REQUEST, "program.program.invalid_details", $validator->errors());
        }
        $category = ProgramCategory::create($data);
        return $this->success('program.category.created', ['id'=>$category->id]);
    }

    /**
     * delete the given program category
     *
     * @param Request $request
     * @param ProgramCategory $programCategory
     * @return mixed
     */
    public function deleteCategory(Request $request, ProgramCategory $programCategory) {
        $programCategory->delete();
        return $this->success('program.category.deleted', ['id'=>$programCategory->id]);
    }

    /**
     * update the given program category
     *
     * @param Request $request
     * @param ProgramCategory $programCategory
     * @return mixed
     */
    public function updateCategory(Request $request ,ProgramCategory $programCategory) {
        $programCategory->update($request->all());
        return $this->success('program.category.updated', ['id'=> $programCategory->id]);
    }

    /**
     * create a program
     *
     * @param Request $request
     * @return mixed
     */
    public function createProgram(Request $request) {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name'=> 'required',
            'type'=>'required',
            'level'=>'required',
        ]);

        if ($validator->fails()) {
            return $this->error(Response::HTTP_BAD_REQUEST, "program.program.invalid_details", $validator->errors());
        }

        $program = Program::createOne($request->all());

        return $this->success('program.program.created', ['id'=>$program->id]);
    }

    /**
     * update a program
     *
     * @param Request $request
     * @param Program $program
     * @return mixed
     */
    public function updateProgram(Request $request, Program $program) {
        $data = $request->all();

        $program->updateIt($data);

        return $this->success('program.program.updated', ['id' => $program->id]);
    }

    /**
     * delete a program
     *
     * @param Request $request
     * @param Program $program
     * @return mixed
     */
    public function deleteProgram(Request $request, Program $program) {

        try{
            $program->tryDeleting();
        }catch(ProgramException $e) {
            report($e);
            return $this->error(Response::HTTP_BAD_REQUEST, $e->getMessage(), ['id' => $program->id]);
        }
        return $this->success('program.program.deleted', ['id' => $program->id]);
    }

    /**
     * query filtered program list
     *
     * @param Request $request
     * @return mixed
     */
    public function programs(Request $request) {
        $filter = $this->createProgramFilter($request);
        $result = Program::search($filter);
        return $this->success('program.program.loaded', $result);
    }

    /**
     * add a stage to the given program
     *
     * @param Request $request
     * @param Program $program
     * @return mixed
     */
    public function addStage(Request $request, Program $program) {
        $stage = $program->addStage($request->all());
        return $this->success('program.stage.created', ['id'=>$stage->id]);
    }

    /**
     * delete the given stage from the given program
     *
     * @param Request $request
     * @param Program $program
     * @param Stage $stage
     * @return mixed
     */
    public function deleteStage(Request $request, Program $program, Stage $stage) {
        if(!$this->validateProgramStructure($program, $stage)){
            return $this->error(Response::HTTP_NOT_FOUND, 'program.stage.not_found');
        }
        $program->removeStage($stage);
        return $this->success('program.stage.deleted', ['id'=>$stage->id]);
    }

    private function createProgramFilter(Request $request) {
        $filter_data = $request->get('filter', []);
        $filter = new ProgramFilter($filter_data);
        if(!Auth::user()->isA(UserType::ADMIN)) {
            $filter->addStatus(EntityStatus::PUBLISHED);
        }
        return $filter;
    }
}
