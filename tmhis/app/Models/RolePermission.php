<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    use HasFactory;

    protected $table = 'role_permissions';
    protected $primaryKey = 'PermissionID';
    public $incrementing = false;

    public $timestamps = false;

    protected $guarded = [];
}
