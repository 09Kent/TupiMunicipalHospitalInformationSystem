<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaboratoryUtilization extends Model
{
    use HasFactory;

    protected $table = 'laboratory_utilization';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $guarded = [];
}
