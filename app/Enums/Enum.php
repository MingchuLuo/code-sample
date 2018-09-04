<?php
/**
 * Enum trait provides common functions for all Enum classes
 *
 * Created by Eason.
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

    /**
     * load all constants into enum items of enum class
     * @throws \ReflectionException
     */
    public static function load() {
        if(!static::$items || !is_array(static::$items)){
            $refClass = new \ReflectionClass(__CLASS__);
            static::$items = $refClass->getConstants();
        }
    }

	protected static function glue($glue=',') {
        return $glue;
    }

    /**
     * join all values into a string with glue
     * @return string
     */
    public static function asString() {
	    return implode(static::glue(), static::values());
    }

    /**
     * validate if the given value a valid enum item
     *
     * @param string $value
     * @param null $default
     * @return string
     */
    public static function validate(string $value, $default=null) {
	    return in_array($value, static::values()) ? $value : ($default ?: static::getDefault());
    }

    public static function getDefault() {
	    return static::values()[0];
    }
}
