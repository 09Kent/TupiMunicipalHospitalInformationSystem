<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyStockMovement extends Model
{
    use HasFactory;

    protected $table = 'pharmacy_stock_movements';
    protected $primaryKey = 'MovementID';

    const CREATED_AT = 'CreatedAt';
    const UPDATED_AT = null;

    protected $guarded = [];
}
