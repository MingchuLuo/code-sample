<?php

namespace App\Models\Program;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stage extends Model
{
    use SoftDeletes;
    //
    protected $fillable = ['name', 'description', 'number', 'days', 'program_id'];

    protected $dates = ['deleted_at'];

    public function program() {
        return $this->belongsTo(Program::class);
    }

    public function removeIt() {
        return parent::delete();
    }

    public function sessions() {
        return $this->hasMany(Session::class)->orderBy('day', 'ASC');
    }
}
