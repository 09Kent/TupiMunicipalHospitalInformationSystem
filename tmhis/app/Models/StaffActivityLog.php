<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffActivityLog extends Model
{
    use HasFactory;

    protected $table = 'staff_activity_logs';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $guarded = [];
}
