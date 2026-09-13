<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaboratoryRequest extends Model
{
    use HasFactory;

    protected $table = 'laboratory_requests';
    protected $primaryKey = 'RequestID';

    const CREATED_AT = 'CreatedAt';
    const UPDATED_AT = null;

    protected $guarded = [];
}
