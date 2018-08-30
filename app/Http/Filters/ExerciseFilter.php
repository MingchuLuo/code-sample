<?php
/**
 * Created by PhpStorm.
 * User: wme
 * Date: 15/05/2018
 * Time: 5:29 PM
 */

namespace App\Http\Filters;


class ExerciseFilter extends AbstractFilter {

    protected $regions;
    protected $equipments;
    protected $mode;
    protected $type;

    public function __construct(array $data = array()) {
        parent::__construct($data);
        $this->regions = $this->fetchArray('regions');
        $this->equipments = $this->fetchArray('equipments');
        $this->mode = ensure($this->_data['exercise_mode_id'], 0);
        $this->type = ensure($this->_data['exercise_type_id'], 0);
    }

    public function mode() {
        return $this->single(func_get_args(), 'exercise_mode_id');
    }

    public function type() {
        return $this->single(func_get_args(), 'exercise_type_id');
    }

    public function position() {
        return $this->single(func_get_args(), 'position');
    }


    public function addRegion($region) {
        $this->regions[$region] = 1;
        return $this;
    }

    public function removeRegion($region) {
        if(isset($this->regions[$region])){
            unset($this->regions[$region]);
        }
        return $this;
    }

    public function getRegions() {
        return array_keys($this->regions);
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