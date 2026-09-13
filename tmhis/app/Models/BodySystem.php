<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BodySystem extends Model
{
    use HasFactory;

    protected $table = 'body_systems';
    protected $primaryKey = 'BodySystemID';

    public $timestamps = false;

    protected $guarded = [];
}
