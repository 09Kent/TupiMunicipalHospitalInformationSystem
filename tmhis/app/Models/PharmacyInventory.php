<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyInventory extends Model
{
    use HasFactory;

    protected $table = 'pharmacy_inventory';
    protected $primaryKey = 'InventoryID';

    public $timestamps = false;

    protected $guarded = [];
}
