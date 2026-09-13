<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingInvoice extends Model
{
    use HasFactory;

    protected $table = 'billing_invoices';
    protected $primaryKey = 'InvoiceID';

    const CREATED_AT = 'CreatedAt';
    const UPDATED_AT = null;

    protected $guarded = [];
}
