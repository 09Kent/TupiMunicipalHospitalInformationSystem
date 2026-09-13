<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'UserID';
    public $incrementing = true;
    protected $keyType = 'int';

    const CREATED_AT = 'CreatedAt';
    const UPDATED_AT = 'UpdatedAt';

    protected $fillable = ['FirstName', 'LastName', 'Username', 'PasswordHash', 'Role', 'Email', 'Status', 'CreatedAt', 'UpdatedAt'];

    protected $hidden = [
        'PasswordHash',
    ];

    public function getAuthPassword()
    {
        return $this->PasswordHash;
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class, 'UserID', 'UserID');
    }
}
