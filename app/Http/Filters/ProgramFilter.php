<?php
/**
 * Created by PhpStorm.
 * User: wme
 * Date: 15/05/2018
 * Time: 5:29 PM
 */

namespace App\Http\Filters;


class ProgramFilter extends AbstractFilter
{

    protected $categories;

    protected $types;

    protected $levels;

    protected $status;

    protected $equipments;

    protected $statuses;

    public function __construct(array $data = array()) {
        parent::__construct($data);
        $this->categories = $this->fetchArray('categories');
        $this->types = $this->fetchArray('types');
        $this->levels = $this->fetchArray('levels');
        $this->equipments = $this->fetchArray('equipments');
        $this->statuses = $this->fetchArray('statuses');
    }

    public function getCategories() {
        return array_keys($this->categories);
    }

    public function getTypes() {
        return array_keys($this->types);
    }

    public function getLevels() {
        return array_keys($this->levels);
    }

    public function getStatuses() {
        return array_keys($this->statuses);
    }

    public function addCategory($category) {
        $this->categories[$category] = 1;
        return $this;
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

    public function addCategories($categories) {
        $cats = explode(',', $categories);
        foreach($cats as $cat) {
            $this->addCategory($cat);
        }
        return $this;
    }

    public function removeCategory($category) {
        if(isset($this->categories[$category])){
            unset($this->categories[$category]);
        }
        return $this;
    }

    public function addLevel($level) {
        $this->levels[$level] = 1;
        return $this;
    }

    public function addLevels($levels) {
        $lvs = explode(',', $levels);
        foreach($lvs as $lv) {
            $this->addLevel($lv);
        }
        return $this;
    }

    public function removeLevel($level) {
        if(isset($this->levels[$level])){
            unset($this->levels[$level]);
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

    public function addEquipment($equipment) {
        $this->equipments[$equipment] = 1;
        return $this;
    }
    public function getEquipments() {
        return array_keys($this->equipments);
    }

    public function removeEquipment($equipment) {
        if(isset($this->equipments[$equipment])){
            unset($this->equipments[$equipment]);
        }
        return $this;
    }
}