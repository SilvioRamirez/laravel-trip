<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    //Arreglo de Mass Assign, asi no se debe especificar los campos con la opcion fillable
    protected $guarded = [];

    //permite tomar la informacion que se envia para el registro antes de que alcance la base de datos y los podemos transformar
    protected $casts = [
        'origin' => 'array',
        'destination' => 'array',
        'driver_location' => 'array',
        'is_started' => 'boolean',
        'is_completed' => 'boolean',
    ];

    public function user()
    {
        //Un viaje pertenece a un usuario
        return $this->belongsTo(User::class);
    }

    public function driver()
    {
        //Un viaje pertene a un driver
        return $this->belongsTo(Driver::class);
    }
    
}
