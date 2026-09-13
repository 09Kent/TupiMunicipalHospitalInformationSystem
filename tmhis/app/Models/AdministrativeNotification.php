<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdministrativeNotification extends Model
{
    use HasFactory;

    protected $table = 'administrative_notifications';
    protected $primaryKey = 'id';


    protected $guarded = [];
}
