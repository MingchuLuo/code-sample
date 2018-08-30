<?php
/**
 * Created by IntelliJ IDEA.
 * User: wme
 * Date: 5/07/2018
 * Time: 9:22 AM
 */

namespace App\Models\Program;


use App\Models\Activity\UserProgram;
use App\Models\Activity\UserSession;
use App\Models\Activity\UserStage;
use App\User;

trait ProgramValidation
{

    public function validateProgramStructure(Program $program, Stage $stage, Session $session=null, $session_unit=null) {

        if(!$program || !$stage) {
            return false;
        }
        if($stage->program_id!=$program->id) {
            return false;
        }
        if($session && $session->stage_id != $stage->id) {
            return false;
        }
        if($session_unit && $session_unit->session_id != $session->id) {
            return false;
        }
        return true;
    }

    public function validateUserProgramStructure(UserProgram $userProgram, UserStage $userStage, UserSession $userSession=null, $session_unit=null) {

        if(!$userProgram || !$userStage) {
            return false;
        }
        if($userStage->user_program_id!=$userProgram->id) {
            return false;
        }
        if($userSession && $userSession->user_stage_id != $userStage->id) {
            return false;
        }
        if($session_unit && $session_unit->user_session_id != $userSession->id) {
            return false;
        }
        return true;

    }

    public function validateOwnership(User $user, UserProgram $userProgram) {
        return $user->id == $userProgram->user_id;
    }

}
