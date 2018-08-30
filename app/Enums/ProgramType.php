<?php
/**
 * Created by IntelliJ IDEA.
 * User: wme
 * Date: 14/06/2018
 * Time: 10:48 AM
 */

namespace App\Enums;


abstract class ProgramType
{
    use Enum;

    public const FREE = "Free";
    public const PREMIUM = "Premium";

}