<?php
/**
 * Created by PhpStorm.
 * User: wme
 * Date: 18/05/2018
 * Time: 10:46 AM
 */

namespace App\Enums;


abstract class SessionType
{

    use Enum;

    const TRAINING = 'Training';
    const TESTING = 'Testing';
    const QUESTIONNAIRE = 'Questionnaire';
    const BONUS = "Bonus";
}
