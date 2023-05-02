<?php

namespace App\Http\Controllers;

use App\Notifications\LoginNeedsVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

use App\Models\User;

class LoginController extends Controller
{
    public function submit(Request $request)
    {
        //Es un poco diferente a lo habitual debido a que se estara usando un codigo de un solo uso enviado a traves de un numero de telefono

        //validate the phone number
        $request->validate([
            //'attribute' => 'rules|'
            'phone' => 'required|numeric|min:14'
        ]);


        // find or create a user model
        // array de atributos, primero lo trata de encontrar sino lo crea

        $user = User::firstOrCreate([
            'phone' => $request->phone
        ]);

        //Si por alguna razon no se crea o no se consigue un usuario con ese numero de telefono
        if (!$user) { 
            return response()->json(['message' => 'Could not process a user with that phone numer.'], 401);
        }

        //send the user a one-time use code
        //el metodo notify se encuentra en el modelo de usuario, permite enviar de manera facil notificaciones al usuario a traves de algun metodo como webhoks, email, entre otros, en este caso se envia un mensajde texto con twilio
        //este metodo espera una clase que debe ser creada con el metodo make:notification
        //se instancia el objeto que creamos 

        $user->notify(new LoginNeedsVerification());

        //return back a responde

        return response()->json(['message' => 'Text message notification send.']);
    }

    public function verify(Request $request)
    {

        //validate the incoming request
        $request->validate([
            'phone' => 'required|numeric|min:14',
            'login_code' => 'required|numeric|between:111111,999999'
        ]);

        //find the user
        $user = User::where('phone', $request->phone)
            ->where('login_code', $request->login_code)
            ->first();

        //is the code provided the same one saved?
        //if so, return back an auth token
        //llama al metodo createToken si todo funciona bien, ese metodo espera un nombre, por lo que se envia el mismo login code, con el metodo plantext nos aseguramos que la respuesta eso sea lo unico que envia
        if ($user) {

            //vaciamos el login_code para que en caso de que solicite uno nuevo este en blanco y no haya problemas
            $user->update([
                'login_code' => null
            ]);

            return $user->createToken($request->login_code)->plainTextToken;

        }

        //if not, return back a message, en caso de que no se cumpla la verificacion del codigo envia
        return response()->json(['message' => 'Invalid verification code.'], 401);
    }

    public function prueba(Request $request)
    {
        return response()->json(['message' => 'Mensaje de prueba api laravel.']);
    }
}
