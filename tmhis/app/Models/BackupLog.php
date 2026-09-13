<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackupLog extends Model
{
    use HasFactory;

    protected $table = 'backup_logs';
    protected $primaryKey = 'BackupID';

    const CREATED_AT = 'CreatedAt';
    const UPDATED_AT = null;

    protected $guarded = [];
}
