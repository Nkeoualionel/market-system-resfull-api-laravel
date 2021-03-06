<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    const UNVERIFIED_USER = '0';
    const VERIFIED_USER = '1';

    const REGULAR_USER = 1;
    const ADMIN_USER = 0;

    protected $table = "users";
    protected $date = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'verified',
        'verification_token',
        'admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function isVerified() {
        if($this->verified == User::UNVERIFIED_USER) {
            return false;
        }

        return true;
    }


    public function isAdmin() {
        if($this->admin == User::REGULAR_USER) {
            return false;
        }

        return true;
    }

    
    public static function generateVerificationCode() {
        return Str::random(50);
    }
}
