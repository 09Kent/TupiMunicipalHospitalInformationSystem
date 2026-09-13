<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingPayment extends Model
{
    use HasFactory;

    protected $table = 'billing_payments';
    protected $primaryKey = 'PaymentID';

    public $timestamps = false;

    protected $guarded = [];
}
