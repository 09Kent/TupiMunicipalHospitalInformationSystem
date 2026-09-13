<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DohComplianceItem extends Model
{
    use HasFactory;

    protected $table = 'doh_compliance_items';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $guarded = [];
}
