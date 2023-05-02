<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    /* protected $fillable = [
        'name',
        'phone',
        'password',
    ]; */

    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'login_code',
        'remember_token',
    ];

    //Modifica lo que trae twilio por defecto que busca en la base de datos un nombre

    public function routeNotificationForTwilio()
    {
        return $this->phone;
    }

    //Define las relaciones con el modelo driver
    public function driver()
    {
        //relacion uno a uno, un usuario solo puede tener un modelo driver asociado
        return $this->hasOne(Driver::class);
    }

    public function trips()
    {
        //Relacion uno a muchos, un usuario puede tener varios viajes asociados
        return $this->hasMany(Trip::class);
    }

}
