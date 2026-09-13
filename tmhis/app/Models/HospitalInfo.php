<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HospitalInfo extends Model
{
    use HasFactory;

    protected $table = 'hospital_info';
    protected $primaryKey = 'HospitalInfoID';

    public $timestamps = false;

    protected $guarded = [];
}
