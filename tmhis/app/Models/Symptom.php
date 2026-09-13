<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Symptom extends Model
{
    use HasFactory;

    protected $table = 'symptoms';
    protected $primaryKey = 'SymptomID';

    public $timestamps = false;

    protected $guarded = [];
}
