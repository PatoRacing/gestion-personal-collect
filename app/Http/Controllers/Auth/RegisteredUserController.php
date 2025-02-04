<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Usuario;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('usuarios.crear-usuario');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'rol' => ['required', 'string', 'max:255'],
            'dni' => ['required', 'string','numeric'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.Usuario::class],
            'telefono' => ['required', 'string', 'regex:/^[0-9]+$/', 'max:20'],
            'domicilio' => ['required', 'string', 'max:255'],
            'localidad' => ['required', 'string', 'max:255'],
            'codigo_postal' => ['required', 'string', 'max:20'],
            'fecha_de_ingreso' => ['required', 'date', 'after_or_equal:1970-01-01'],
            'password' => ['required', 'string', 'min:8', 'regex:/^(?=.*[A-Z])(?=.*[!@#$%^&*])/', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = Usuario::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'rol' => $request->rol,
            'dni' => $request->dni,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'domicilio' => $request->domicilio,
            'localidad' => $request->localidad,
            'codigo_postal' => $request->codigo_postal,
            'fecha_de_ingreso' => $request->fecha_de_ingreso,
            'estado' => 1,
            'ult_modif' => auth()->id(),
            'password' => Hash::make($request->password)
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
