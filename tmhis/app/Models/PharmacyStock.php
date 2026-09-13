<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyStock extends Model
{
    use HasFactory;

    protected $table = 'pharmacy_stocks';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $guarded = [];
}
