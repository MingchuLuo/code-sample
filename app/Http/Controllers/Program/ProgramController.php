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

class ProgramController extends Controller
{
    use ProgramValidation;
    //
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function getProgram(Request $request, Program $program) {
        $program->load(['categories', 'stages.sessions', 'nutritionPlan.sections.files']);
        return $this->success('program.program.loaded', $program);
    }

    public function categories(Request $request) {
        return $this->success('program.category.loaded', ProgramCategory::all());
    }

    public function getCategory(Request $request, ProgramCategory $programCategory) {
        return $this->success('program.category.loaded', $programCategory);
    }

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

    public function deleteCategory(Request $request, ProgramCategory $programCategory) {
        $programCategory->delete();
        return $this->success('program.category.deleted', ['id'=>$programCategory->id]);
    }

    public function updateCategory(Request $request ,ProgramCategory $programCategory) {
        $programCategory->update($request->all());
        return $this->success('program.category.updated', ['id'=> $programCategory->id]);
    }

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

    public function updateProgram(Request $request, Program $program) {
        $data = $request->all();

        $program->updateIt($data);

        return $this->success('program.program.updated', ['id' => $program->id]);
    }

    public function deleteProgram(Request $request, Program $program) {

        try{
            $program->tryDeleting();
        }catch(ProgramException $e) {
            report($e);
            return $this->error(Response::HTTP_BAD_REQUEST, $e->getMessage(), ['id' => $program->id]);
        }
        return $this->success('program.program.deleted', ['id' => $program->id]);
    }

    public function programs(Request $request) {
        $filter = $this->createProgramFilter($request);
        $result = Program::search($filter);
        return $this->success('program.program.loaded', $result);
    }

    public function addStage(Request $request, Program $program) {
        $stage = $program->addStage($request->all());
        return $this->success('program.stage.created', ['id'=>$stage->id]);
    }

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
