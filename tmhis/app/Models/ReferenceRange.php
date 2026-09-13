<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferenceRange extends Model
{
    use HasFactory;

    protected $table = 'reference_ranges';
    protected $primaryKey = 'RangeID';

    public $timestamps = false;

    protected $guarded = [];
}
