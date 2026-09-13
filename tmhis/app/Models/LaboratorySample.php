<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaboratorySample extends Model
{
    use HasFactory;

    protected $table = 'laboratory_samples';
    protected $primaryKey = 'SampleID';

    public $timestamps = false;

    protected $guarded = [];
}
