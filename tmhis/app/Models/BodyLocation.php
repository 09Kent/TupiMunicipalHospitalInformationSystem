<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BodyLocation extends Model
{
    use HasFactory;

    protected $table = 'body_locations';
    protected $primaryKey = 'BodyLocationID';

    public $timestamps = false;

    protected $guarded = [];
}
