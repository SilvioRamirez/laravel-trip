<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $guarded = [];

    //Define las relaciones a la inversa ->las que tiene driver con usuario
    
    public function user()
    {
        //Un driver pertene a un usuario
        return $this->belongsTo(User::class);
    }

    public function trips()
    {
        //Un driver puede tener multiples viajes asociados
        return $this->hasMany(Trip::class);
    }
}
