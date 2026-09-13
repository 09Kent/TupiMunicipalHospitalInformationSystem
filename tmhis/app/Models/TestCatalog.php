<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestCatalog extends Model
{
    use HasFactory;

    protected $table = 'test_catalog';
    protected $primaryKey = 'CatalogID';

    public $timestamps = false;

    protected $guarded = [];
}
