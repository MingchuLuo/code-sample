<?php

namespace App\Http\Controllers\Program;


use App\BonusExercise;
use App\Http\Controllers\Controller;
use App\Http\Filters\BonusSessionFilter;
use Illuminate\Http\Request;


/**
 * manage bonus exercises, similar to exercise template
 *
 * Created by Eason
 * Date: 30/07/2018
 * Time: 9:35 AM
 */


class BonusExerciseController extends Controller {

    public function __construct(Request $request){
        parent::__construct($request);
    }

    public function bonusExercises(Request $request) {
        //$filter = $this->createBonusExerciseFilter($request);
        $filter_data = $request->get('filter', []);
        $filter = new BonusSessionFilter($filter_data);

        $result = BonusExercise::search($filter);
        return $this->success('exercise.bonus_exercise.loaded', $result);
    }

    public function getBonusExercise(Request $request, BonusExercise $bonusExercise) {
        return $this->success('exercise.bonus_exercise.loaded', $bonusExercise);
    }

    public function createBonusExercise(Request $request) {

        $data = $request->all();

        $bonusExercise = BonusExercise::createOne($data);

        return $this->success('exercise.bonus_exercise.created', ['id'=>$bonusExercise->id]);
    }

    public function updateBonusExercise(Request $request, BonusExercise $bonusExercise) {

        $data = $request->all();

        $bonusExercise->updateIt($data);

        return $this->success('exercise.bonus_exercise.updated', ['id'=>$bonusExercise->id]);
    }


    public function deleteBonusExercise(Request $request, BonusExercise $bonusExercise) {

        $bonusExercise->deleteIt();

        return $this->success('exercise.bonus_exercise.deleted', ['id'=>$bonusExercise->id]);
    }

    public function publishBonusExercise(Request $request, BonusExercise $bonusExercise) {

        $bonusExercise->publish();

        return $this->success('exercise.bonus_exercise.published', ['id'=>$bonusExercise]);

    }

}
