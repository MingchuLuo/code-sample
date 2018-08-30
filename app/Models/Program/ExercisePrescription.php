<?php

namespace App\Models\Program;

use App\Models\Prescription\PrescriptionAttribute;
use App\Models\Prescription\PrescriptionUnit;
use Illuminate\Database\Eloquent\Model;

class ExercisePrescription extends Model
{

    protected $fillable = ['exercise_id', 'prescription_attribute_id', 'prescription_unit_id', 'value'];

    public function exercise() {
        return $this->belongsTo(Exercise::class, 'exercise_id');
    }

    public function attribute() {
        return $this->belongsTo(PrescriptionAttribute::class, 'prescription_attribute_id');
    }

    public function unit() {
        return $this->belongsTo(PrescriptionUnit::class, 'prescription_unit_id');
    }
}
