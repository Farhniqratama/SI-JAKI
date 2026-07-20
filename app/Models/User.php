<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'pokja',
        'akses',
        'last_login',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login' => 'datetime',
        ];
    }

    // Metode untuk memeriksa apakah user memiliki akses tertentu
    public function hasRole($role)
    {
        return $this->akses === $role;
    }

    public function getPokjaAttribute($value)
    {
        // Jika value adalah string JSON, decode ke array
        $pokjaArray = is_string($value) ? json_decode($value, true) : $value;
        
        // Kembalikan sebagai string yang sudah diimplode
        return is_array($pokjaArray) ? implode(', ', $pokjaArray) : $value;
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // Metode untuk memeriksa apakah user Developer
    public function isDev()
    {
        return $this->akses === 'Dev';
    }

    // Metode untuk memeriksa apakah user Admin
    public function isAdmin()
    {
        return $this->akses === 'Admin';
    }
    
    // Metode untuk memeriksa apakah user biasa
    public function isUser()
    {
        return $this->akses === 'User';
    }
}
