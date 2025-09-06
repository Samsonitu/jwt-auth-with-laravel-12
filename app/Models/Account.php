<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Account extends Authenticatable implements JWTSubject
{
    protected $table = 'accounts';

    protected $fillable = [
        'user_name',
        'password',
    ];

    protected $casts = [
        'user_name' => 'string',
        'password' => 'string',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();  
    }

    public function getJWTCustomClaims()
    {
        return [];   
    }
}
