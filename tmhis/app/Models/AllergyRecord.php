<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllergyRecord extends Model
{
    use HasFactory;

    protected $table = 'allergy_records';
    protected $primaryKey = 'AllergyID';

    const CREATED_AT = 'CreatedAt';
    const UPDATED_AT = null;

    protected $guarded = [];
}
