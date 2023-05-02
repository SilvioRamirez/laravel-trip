<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function show(Request $request)
    {
        
        //Devuelve el usuario asociado con un modelo driver

        $user = $request->user();
        //Devuelve información si el usuario tiene un driver asociado
        $user->load('driver');

        return $user;

    }

    public function update(Request $request)
    {
        
        $request->validate([
            'year' => 'required|numeric|between:1999,2024',
            'make' => 'required',
            'model' => 'required',
            'color' => 'required|alpha',
            'license_plate' => 'required',
            'name' => 'required'

        ]);

        $user = $request->user();
        //Solo actualiza el nombre de este usuario en el modelo
        $user->update($request->only('name'));

        //create or update a driver associeated with this user
        //esto nos devuelve el objeto driver de eloquent
        $user->driver()->updateOrCreate($request->only([
            'year',
            'make',
            'model',
            'color',
            'license_plate'
        ]));

        $user->load('driver');

        return $user;

    }
}
