<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintCondition extends Model
{
    use HasFactory;

    protected $table = 'complaint_conditions';
    protected $primaryKey = 'ComplaintConditionID';

    public $timestamps = false;

    protected $guarded = [];
}
