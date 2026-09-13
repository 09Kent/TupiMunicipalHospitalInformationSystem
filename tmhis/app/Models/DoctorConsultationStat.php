<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorConsultationStat extends Model
{
    use HasFactory;

    protected $table = 'doctor_consultation_stats';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $guarded = [];
}
