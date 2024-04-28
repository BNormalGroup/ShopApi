<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Users extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'lastName',
        'firstName',
        'birthday',
        'email',
        'password',
        'isAdmin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function bans()
    {
        return $this->hasMany(Bannes::class, 'user_id', 'id');
    }

    public function orders()
    {
        return $this->hasMany(HistoryOrders::class, 'user_id','id');
    }
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        $isBanned = Bannes::where('user_id', $this->id)->exists();

        return [
            "id" => $this->id,
            "email" => $this->email,
            "firstName" => $this->firstName,
            "isAdmin" => $this->isAdmin,
            "lastName" => $this->lastName,
            "birthday" => $this->birthday,
            "isBanned" => $isBanned
        ];
    }
}
