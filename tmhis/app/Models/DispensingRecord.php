<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DispensingRecord extends Model
{
    use HasFactory;

    protected $table = 'dispensing_records';
    protected $primaryKey = 'DispenseID';

    public $timestamps = false;

    protected $guarded = [];
}
