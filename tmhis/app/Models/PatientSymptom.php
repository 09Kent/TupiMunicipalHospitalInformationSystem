<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientSymptom extends Model
{
    use HasFactory;

    protected $table = 'patient_symptoms';
    protected $primaryKey = 'PatientSymptomID';

    public $timestamps = false;

    protected $guarded = [];
}
