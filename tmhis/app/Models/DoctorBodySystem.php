<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorBodySystem extends Model
{
    use HasFactory;

    protected $table = 'doctor_body_systems';
    protected $primaryKey = 'DoctorSystemID';

    public $timestamps = false;

    protected $guarded = [];
}
