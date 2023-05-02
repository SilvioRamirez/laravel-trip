<?php

namespace App\Http\Controllers;

use App\Events\TripAccepted;
use App\Events\TripEnded;
use App\Events\TripLocationUpdated;
use App\Events\TripStarted;
use Illuminate\Http\Request;

use App\Models\Trip;

class TripController extends Controller
{
    
    public function store(Request $request)
    {
        //validar la informacion, estos tres son los campos que requiere el usuario antes de q un conductor acepte el viaje
        $request->validate([
            'origin' => 'required',
            'destination' => 'required',
            'destination_name' => 'required'
        ]);

        return $request->user()->trips()->create($request->only([
            'origin',
            'destination',
            'destination_name'
        ]));

    }

    public function show(Request $request, Trip $trip)
    {
        //Retorna el modelo trip acorde a la key (id) del viaje que se envia en la ruta
        //necesitamos tambien saber que el viaje pertenece al usuario que esta autenticado
        //debemos hacerlo de manera que nos traiga el id del driver y del viaje al usuario
        //la solicitud la pueden hacer tanto el usuario como el conductor (driver)

        //si el id del usuario del viaje es igual al id del usuario que hace el request -> bien
        if ($trip->user->id == $request->user()->id){
            return $trip;
        }

        //si el viaje tiene un driver y si el usuario tiene un driver (para comprobar que existan y que no sean nulos ya que sino seria verdadera la condicion de abajo algunas veces)
        if ($trip->driver && $request->user()->driver){
            //si el id del driver que tiene el viaje es igual al id del driver que tiene asignado el usuario
            if ($trip->driver->id == $request->user()->driver->id){
                return $trip;
            }
        }

        return response()->json(['message' => 'Cannot find this trip.'], 404);

    }

    public function accept(Request $request, Trip $trip)
    {
        //driver acepta el trip, el drip se debe actualizar, su estado

        //se espera que con el request se envie la localizacion del driver, se valida
        $request->validate([
            'driver_location' => 'required'
        ]);

        $trip->update([
            //eso se ejecuta cuando el driver acepta el viaje, el viaje esta asociado a un usuario
            'driver_id' => $request->user()->id,
            //Busca la localizacion del driver, donde esta cuando acepto
            'driver_location' => $request->driver_location,
        ]);

        //tambien se envie la información del driver para que el cliente sepa que driver acepto el trip driver.user carga esa relacion
        $trip->load('driver.user');

        //se deben crear eventos de clases para hacer los push de notificaciones en tiempo real cuando por ejemplo el driver acepte el viaje, automanticamente laravel debe avisar al frontend que el viaje fue aceptado para que se notifique al cliente
        TripAccepted::dispatch($trip, $request->user);
        
        
        
        //retorna al cliente
        return $trip;


    }

    public function start(Request $request, Trip $trip)
    {
        //driver comienza a llevar al pasajero a su destino
        //actualiza el estado del viaje, comienza, se recogio el pasajero y comienza
        $trip->update([
            'is_started' => true
        ]);

        $trip->load('driver.user');

        TripStarted::dispatch($trip, $request->user);
        
        return $trip;

    }

    public function end(Request $request, Trip $trip)
    {
        //driver termina el viaje en el destino
        //driver termina el trip
        //actualiza el estado del viaje, termina, se dejo el pasajero en su destino
        $trip->update([
            'is_complete' => true
        ]);

        $trip->load('driver.user');

        TripEnded::dispatch($trip, $request->user);

        return $trip;
    }

    public function location(Request $request, Trip $trip)
    {
        //actualiza la localizacion
        $request->validate([
            'driver_location' => 'required'
        ]);

        $trip->update([
            'driver_location' => $request->driver_location
        ]);

        $trip->load('driver.user');

        TripLocationUpdated::dispatch($trip, $request->user);

        return $trip;
    }
}
