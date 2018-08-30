<?php

namespace App\Models\Program;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramCategory extends Model
{
    //
    use SoftDeletes;

    protected $fillable = ['name', 'description'];

    protected $dates = ['deleted_at'];

    public function programs() {
        return $this->belongsToMany(Program::class, 'program_has_categories');
    }
}
