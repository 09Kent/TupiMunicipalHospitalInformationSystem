<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecordReleaseRequest extends Model
{
    use HasFactory;

    protected $table = 'record_release_requests';
    protected $primaryKey = 'RequestID';

    public $timestamps = false;

    protected $guarded = [];
}
