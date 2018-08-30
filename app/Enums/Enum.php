<?php
/**
 * Created by PhpStorm.
 * User: wme
 * Date: 28/05/2018
 * Time: 10:19 AM
 */

namespace App\Enums;


trait Enum
{

    protected static $items;

	public static function values() {
        return array_values(static::items());
	}

	public static function items() {
        static::load();
        return static::$items;
    }

    public static function load() {
        if(!static::$items || !is_array(static::$items)){
            $refClass = new \ReflectionClass(__CLASS__);
            static::$items = $refClass->getConstants();
        }
    }

	protected static function glue($glue=',') {
        return $glue;
    }

    public static function asString() {
	    return implode(static::glue(), static::values());
    }

    public static function validate(string $value, $default=null) {
	    return in_array($value, static::values()) ? $value : ($default ?: static::getDefault());
    }

    public static function getDefault() {
	    return static::values()[0];
    }
}
