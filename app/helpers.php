<?php

if(!function_exists('ensure')) {
	function ensure(&$var, $default=null){
        return isset($var) ? $var : $default;
    }
}

if(!function_exists('array_values_selected')) {
    function array_random_values($array, $min=1, $max=1) {
        $size = count($array);
        $min = min(max(1, $min), $size);
        $max = max(min($size, $max), 1);
        $num = rand($min, $max);
        $indices = (array)array_rand($array, $num);
        $values = [];
        foreach($indices as $index) {
            $values[] = $array[$index];
        }
        return $values;
    }
}
