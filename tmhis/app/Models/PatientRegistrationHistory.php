<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientRegistrationHistory extends Model
{
    use HasFactory;

    protected $table = 'patient_registration_history';
    protected $primaryKey = 'HistoryID';

    const CREATED_AT = 'CreatedAt';
    const UPDATED_AT = null;

    protected $guarded = [];
}
