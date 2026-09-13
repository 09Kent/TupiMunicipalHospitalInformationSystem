<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingRevenueSummary extends Model
{
    use HasFactory;

    protected $table = 'billing_revenue_summary';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $guarded = [];
}
