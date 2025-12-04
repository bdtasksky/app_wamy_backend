<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPasswordForWebUserNotification;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class WebUser extends Authenticatable implements AuthenticatableContract, JWTSubject
{
    use HasFactory, Notifiable;
    protected $table = 'web_users';

    protected $fillable = [
        "social_id",
        "name",
        "email",
        "password",
        "remember_token",
        "status",
        "avatar",
        "bg_image"
    ];

    protected $casts = [
        "social_id" => "string",
        "name" => "string",
        "email" => "string",
        "password" => "string",
        "status" => "boolean",
        "avatar" => "string",
        "bg_image" => "string"
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordForWebUserNotification($token));
    }
    /**
     * JWTSubject methods
     */

    /**
     * Return the identifier that will be stored in the subject claim of the JWT
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        // Add any custom claims you want in the token; empty array is fine.
        return [];
    }
}
