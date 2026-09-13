<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationalReport extends Model
{
    use HasFactory;

    protected $table = 'operational_reports';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $guarded = [];
}
