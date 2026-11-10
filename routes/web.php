<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\AdvertisementController;
use App\Http\Controllers\HeaderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SurveillanceController;

// Página principal
Route::get('/', [HomeController::class, 'index'])->name('home');

// Listado público de noticias
Route::get('/noticias', [NewsController::class, 'publicIndex'])->name('news.public');

// Páginas informativas
Route::get('/contactenos', [PageController::class, 'contact'])->name('pages.contact');
Route::get('/acerca-de-nosotros', [PageController::class, 'about'])->name('pages.about');
Route::get('/paginas/editar', [PageController::class, 'edit'])->middleware('auth')->name('pages.edit');
Route::put('/paginas/actualizar', [PageController::class, 'update'])->middleware('auth')->name('pages.update');

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

// Videovigilancia
Route::middleware('auth')->prefix('surveillance')->name('surveillance.')->group(function () {
    Route::get('/', [SurveillanceController::class, 'index'])->name('index');
    Route::get('/cameras/create', [SurveillanceController::class, 'create'])->name('cameras.create');
    Route::post('/cameras', [SurveillanceController::class, 'store'])->name('cameras.store');
    Route::get('/cameras/{camera}/edit', [SurveillanceController::class, 'edit'])->name('cameras.edit');
    Route::put('/cameras/{camera}', [SurveillanceController::class, 'update'])->name('cameras.update');
    Route::delete('/cameras/{camera}', [SurveillanceController::class, 'destroy'])->name('cameras.destroy');
});

// CRUD Noticias
Route::get('/news', [NewsController::class, 'index'])
    ->middleware('auth')
    ->name('news.index');
Route::resource('news', NewsController::class)->except(['index', 'show'])
    ->middleware('auth');
Route::get('/news/{news}', [NewsController::class, 'show'])->name('news.show');

// Secciones públicas y administrativas
Route::get('/secciones/{section}', [SectionController::class, 'show'])->name('sections.show');
Route::get('sections/{section}/news', [SectionController::class, 'news'])->middleware('auth')->name('sections.news.index');
Route::resource('sections', SectionController::class)->except(['show'])
    ->middleware('auth');

// CRUD Publicidad
Route::resource('advertisements', AdvertisementController::class);

// CRUD Encabezado
Route::get('/header/edit', [HeaderController::class, 'edit'])->name('header.edit');
Route::put('/header/update', [HeaderController::class, 'update'])->name('header.update');

// CRUD Usuarios
Route::resource('users', UserController::class)
    ->except(['show'])
    ->middleware('auth');

// Rutas de autenticación
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Illuminate\Http\Request $request) {
    $credentials = $request->only('email', 'password');
    
    if (auth()->attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/dashboard');
    }
    
    return back()->withErrors([
        'email' => 'Las credenciales no coinciden con nuestros registros.',
    ]);
});

Route::post('/logout', function (Illuminate\Http\Request $request) {
    auth()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');