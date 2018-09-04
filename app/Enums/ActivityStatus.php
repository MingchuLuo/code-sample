<?php
/**
 * Created by Eason
 * Date: 28/05/2018
 * Time: 10:19 AM
 */

namespace App\Enums;

abstract class ActivityStatus
{
    use Enum;

    const NOT_STARTED = "NotStarted";

    const STARTED = "Started";

    const SKIPPED = "Skipped";

    const PAUSED = "Paused";

    const COMPLETED = "Completed";
}
