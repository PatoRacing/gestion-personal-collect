<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        //Validacioon de credenciales de acceso
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        //Datos de inicio de sesion y acceso
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            //Verificacion de estado del usuario: en caso de que sea 2 (inactivo) se bloquea y se redirige
            if ($user->estado === 2) {
                Auth::user()->tokens->each(function ($token, $key) {
                    $token->delete();
                });
                Auth::logout();
                return redirect()->route('login')
                    ->withErrors(['email' => 'Acceso denegado. Tu estado es inactivo.']);
            }

            // Si el usuario no está bloqueado, permite el inicio de sesión
            $request->session()->regenerate();
            return redirect()->intended(RouteServiceProvider::HOME);
        }
        return redirect()->route('login')
            ->withErrors(['email' => 'Las credenciales proporcionadas son incorrectas.']);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
