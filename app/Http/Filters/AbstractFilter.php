<?php
/**
 * Created by PhpStorm.
 * User: wme
 * Date: 15/05/2018
 * Time: 4:33 PM
 */

namespace App\Http\Filters;


use App\Http\Helpers\HttpHelper;
use Illuminate\Http\Request;

class AbstractFilter {

    protected $_data;

    protected $order;

    /**
     * AbstractFilter constructor.
     * protected. Make sure this class is not used directly
     * @param array $data
     */
    protected function __construct($data = array()) {
        $this->_data = $data;
        $this->order = array();
        if(isset($this->_data['order'])){
            $order = $this->_data['order'];
            $this->order[] = explode(',', $order);
        }
    }

    protected function fetchArray($key, $delimiter=',', $default=[]) {
        return isset($this->_data[$key]) ? array_flip(explode($delimiter, $this->_data[$key])) : $default;
    }

    public function limit() {
        return $this->single(func_get_args(), 'limit', 10);
    }

    public function offset() {
        return $this->single(func_get_args(), 'offset', 0);
    }

    public function keyword() {
        return $this->single(func_get_args(), 'keyword');
    }

    public function order() {
        $args = func_get_args();
        if(count($args)==0){
            return $this->order;
        }
        if(is_string($args[0])){
            $this->order[] = [$args[0], ensure($args[1], 'ASC')];
            return $this;
        }
        if(is_array($args[0])){
            foreach($args[0] as $o) {
                $this->order[] = $o;
            }
            return $this;
        }
        throw new Exception('invalid arguments');
    }

    protected function multiple($args, $attr){
        if(isset($args[0])){
            $this->$attr[strval($args[0])] = 1;
            return $this;
        }else {
            return array_keys($this->$attr);
        }
    }

    protected function single($args, $attr, $default=null){
        if(isset($args[0])){
            $this->_data[$attr]= $args[0];
            return $this;
        }else{
            return ensure($this->_data[$attr], $default);
        }
    }
}