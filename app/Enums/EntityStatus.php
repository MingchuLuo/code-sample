<?php
/**
 * Created by PhpStorm.
 * User: wme
 * Date: 28/05/2018
 * Time: 10:19 AM
 */

namespace App\Enums;

abstract class EntityStatus
{
    use Enum;

    const DRAFT = 'Draft';
    const PUBLISHED = 'Published';
    const EXPIRED = 'Expired';
    const UNAVAILABLE = 'Unavailable';
}