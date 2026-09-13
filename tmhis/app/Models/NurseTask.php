<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NurseTask extends Model
{
    use HasFactory;

    protected $table = 'nurse_tasks';
    protected $primaryKey = 'TaskID';

    const CREATED_AT = 'CreatedAt';
    const UPDATED_AT = null;

    protected $guarded = [];
}
