<?php
/**
 * Created by IntelliJ IDEA.
 * User: wme
 * Date: 14/06/2018
 * Time: 10:50 AM
 */

namespace App\Enums;


abstract class ProgramLevel
{

    use Enum;

    public const BEGINNER = 1;
    public const INTERMEDIATE = 2;
    public const ADVANCED = 3;

}