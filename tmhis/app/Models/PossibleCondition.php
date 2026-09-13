<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PossibleCondition extends Model
{
    use HasFactory;

    protected $table = 'possible_conditions';
    protected $primaryKey = 'ConditionID';

    public $timestamps = false;

    protected $guarded = [];
}
