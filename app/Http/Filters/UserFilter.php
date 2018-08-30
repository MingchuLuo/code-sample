<?php
/**
 * Created by IntelliJ IDEA.
 * User: wme
 * Date: 16/07/2018
 * Time: 3:40 PM
 */

//namespace App\Http\Controllers\User;
namespace App\Http\Filters;

use App\Http\Controllers\User;
//use App\Http\Filters\AbstractFilter;

class UserFilter extends AbstractFilter
{
    protected $types;

    protected $status;

    /**
     * UserFilter constructor.
     * @param $filter_data
     */
    public function __construct($filter_data)
    {
        parent::__construct($filter_data);
        $this->types = $this->fetchArray('types');
        $this->statuses = $this->fetchArray('statuses');
    }

    public function getTypes() {
        return array_keys($this->types);
    }

    public function getStatuses() {
        return array_keys($this->statuses);
    }

    public function addStatus($status) {
        $this->statuses[$status] = 1;
        return $this;
    }

    public function addStatuses($statuses) {
        $statuses = explode(',', $statuses);
        foreach($statuses as $status) {
            $this->addStatus($status);
        }
        return $this;
    }

    public function removeStatus($status) {
        if(isset($this->statuses[$status])){
            unset($this->statuses[$status]);
        }
        return $this;
    }

    public function addType($type) {
        $this->types[$type] = 1;
        return $this;
    }

    public function addTypes($types) {
        $tps = explode(',', $types);
        foreach($tps as $tp) {
            $this->addType($tp);
        }
        return $this;
    }

    public function removeType($type) {
        if(isset($this->types[$type])){
            unset($this->types[$type]);
        }
        return $this;
    }


}