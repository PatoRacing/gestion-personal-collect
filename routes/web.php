<?php

use App\Http\Controllers\AcuerdoController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BuscadorController;
use App\Http\Controllers\CarteraController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CuotaController;
use App\Http\Controllers\Gestioncontroller;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsuarioController;
use App\Models\GestionDeudor;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Vistas abiertas
Route::get('/', [AuthenticatedSessionController::class, 'create'])->middleware(['usuario.logueado'])->name('/');
Route::post('/', [AuthenticatedSessionController::class, 'store']);
Route::get('olvide-password', [PasswordResetLinkController::class, 'create'])->name('olvide.password');
Route::post('olvide-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');

//Vistas exclusivas para usuarios autenticados
//1- Perfil
Route::get('perfil', [PerfilController::class, 'index'])->middleware(['auth', 'verified'])->name('perfil');
//2-Usuarios
Route::get('usuarios', [UsuarioController::class, 'index'])->middleware(['auth', 'verified', 'rol.administrador'])->name('usuarios');
//3-Clientes
Route::get('clientes', [ClienteController::class, 'index'])->middleware(['auth', 'verified', 'rol.administrador'])->name('clientes');
Route::get('perfil-cliente/{id}', [ClienteController::class, 'perfilCliente'])->middleware(['auth', 'verified', 'rol.administrador', 'validar.id'])->name('perfil.cliente');
Route::get('perfil-producto/{id}', [ClienteController::class, 'perfilProducto'])->middleware(['auth', 'verified', 'rol.administrador', 'validar.id'])->name('perfil.producto');
//4-Cartera
Route::get('cartera', [CarteraController::class, 'index'])->middleware(['auth', 'verified'])->name('cartera');
Route::get('deudor-perfil/{id}', [CarteraController::class, 'deudorPerfil'])->middleware(['auth', 'verified', 'validar.id'])->name('deudor.perfil');
//5-Gestiones sobre operacion
Route::get('gestiones', [Gestioncontroller::class, 'index'])->middleware(['auth', 'verified'])->name('gestiones');
Route::get('operacion-perfil/{id}', [Gestioncontroller::class, 'operacionPerfil'])->middleware(['auth', 'verified', 'validar.id'])->name('operacion.perfil');
//6-Acuerdo
Route::get('acuerdos', [AcuerdoController::class, 'index'])->middleware(['auth', 'verified'])->name('acuerdos');
Route::get('acuerdo-perfil/{id}', [AcuerdoController::class, 'acuerdoPerfil'])->middleware(['auth', 'verified', 'validar.id'])->name('acuerdo.perfil');
//7-Cuotas
Route::get('cuotas', [CuotaController::class, 'index'])->middleware(['auth', 'verified'])->name('cuotas');
Route::get('cuota-perfil/{id}', [CuotaController::class, 'cuotaPerfil'])->middleware(['auth', 'verified', 'validar.id'])->name('cuota.perfil');
//8-Buscador
Route::get('buscador', [BuscadorController::class, 'index'])->middleware(['auth', 'verified'])->name('buscador');

//Revisar
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
