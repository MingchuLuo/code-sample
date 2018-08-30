<?php

namespace App\Models\Program;

use App\Enums\EntityStatus;
use App\Enums\ProgramLevel;
use App\Enums\ProgramType;
use App\Exceptions\ProgramException;
use App\Http\Filters\ProgramFilter;
use App\Models\Nutrition\NutritionPlan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Program extends Model
{
    //
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'status', 'type', 'level', 'nutrition_plan_id', 'inclusions', 'equipments', 'expected_outcomes', 'commitment', 'cover_path', 'thumbnail_path', 'healthcare_info'];

    protected $dates = ['deleted_at', 'published_at'];

    public static function createOne($data = array())
    {

        $status = ensure($data['status'], '');
        $type = ensure($data['type'], '');
        $level = ensure($data['level'], 0);
        $category_id = ensure( $data['program_category_id'], 0);

        $sanitised = [
            'status' => EntityStatus::validate($status, EntityStatus::DRAFT),
            'type' => ProgramType::validate($type, ProgramType::FREE),
            'level' => ProgramLevel::validate($level, ProgramLevel::BEGINNER),
        ];

        $program = Program::create(array_merge($data, $sanitised));

        $cat_ids = [];
        if($category_id) {
            $cat_ids[] = $category_id;
        }
        $program->categories()->sync($cat_ids);

        return $program;
    }

    public function updateIt($data = array())
    {

        $status = ensure($data['status'], '');
        $type = ensure($data['type'], '');
        $level = ensure($data['level'], 0);
        $category_id = ensure( $data['program_category_id'], 0);

        $sanitised = [
            'status' => EntityStatus::validate($status, EntityStatus::DRAFT),
            'type' => ProgramType::validate($type, ProgramType::FREE),
            'level' => ProgramLevel::validate($level, ProgramLevel::BEGINNER),
        ];

        $result = parent::update(array_merge($data, $sanitised));

        $cat_ids = [];
        if($category_id) {
            $cat_ids[] = $category_id;
        }
        $this->categories()->sync($cat_ids);

        return $result;
    }

    public function tryDeleting() {
        if($this->popularity()>0){
            throw new ProgramException("program.program.cannot_delete");
        }
        parent::delete();
    }

    public function nutritionPlan()
    {
        return $this->belongsTo(NutritionPlan::class);
    }

    public function addStage($stageData = array())
    {
        $sanitised = [
            'program_id' => $this->id,
            'number' => $this->stages()->get()->count() + 1,
            'days' => 7
        ];
        $stage = Stage::create(array_merge($stageData, $sanitised));
        $this->load("stages");
        return $stage;
    }

    public function removeStage(Stage $stage) {
        $stage->removeIt();
        $this->stages()->where('number', '>', $stage->number)->decrement('number');
        $this->load('stages');
    }

    public function stages() {
        return $this->hasMany(Stage::class)->orderBy('number', 'ASC');
    }

    public function sessions() {
        return $this->hasManyThrough(Session::class, Stage::class);
    }

    public function categories() {
        return $this->belongsToMany(ProgramCategory::class, 'program_has_categories');
    }

    public function popularity() {
        return 0;
    }

    public function getStage($id) {
        return Stage::where(['program_id' => $this->id, 'id' => $id])->first();
    }

    public static function search(ProgramFilter $filter)
    {
        $query = static::with('categories')->withCount('stages')
            ->when(!empty($filter->keyword()), function ($query) use ($filter) {
                $query->where(function ($q) use ($filter) {
                    $q->where('name', 'LIKE', '%' . $filter->keyword() . '%')
                        ->orWhere('expected_outcomes', 'LIKE', '%' . $filter->keyword() . '%')
                        ->orWhere('commitment', 'LIKE', '%' . $filter->keyword() . '%')
                        ->orWhere('inclusions', 'LIKE', '%' . $filter->keyword() . '%')
                        ->orWhere('description', 'LIKE', '%' . $filter->keyword() . '%');
                });
            })
            ->when(count($filter->getCategories()) > 0, function ($query) use ($filter) {
                $query->whereHas('categories', function ($q) use ($filter) {
                    $q->whereIn('id', $filter->getCategories());
                });
            })
            ->when(count($filter->getTypes()) > 0, function ($query) use ($filter) {
                $query->whereIn('type', $filter->getTypes());
            })
            ->when(count($filter->getLevels()) > 0, function ($query) use ($filter) {
                $query->whereIn('level', $filter->getLevels());
            })
            ->when(count($filter->getStatuses()) > 0, function ($query) use ($filter) {
                $query->whereIn('status', $filter->getStatuses());
            })
            ->when(count($filter->getEquipments())>0, function ($query) use ($filter) {
                $query->where(function($q) use ($filter) {
                    foreach ($filter->getEquipments() as $equip){
                        $q->orWhere('equipments', 'LIKE', '%,'.$equip.',%');
                    }
                });
            });

        // get total number before adding pagination
        $total = $query->count();
        $query->when($filter->limit()>0, function ($query) use ($filter) {
            $query->skip($filter->offset())->take($filter->limit());
        });
        foreach($filter->order() as $order) {
            list($orderField, $orderBy) = $order;
            $query->orderBy($orderField, $orderBy);
        }

        return array('total'=> $total, 'items' => $query->get());
    }

    public function updateEquipments() {
        $this->load('sessions');
        $this->update(['equipments' => ',' . $this->sessions->reduce(function($carry, $session) {
                $carry = $carry->merge(explode(',', $session->equipments));
                return $carry;
            }, collect([]))->reject(function($item){return empty($item);})->unique()->implode(',') . ',']);
    }

}
