<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentSummary extends Model
{
    use HasFactory;

    protected $table = 'appointment_summaries';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $guarded = [];
}
