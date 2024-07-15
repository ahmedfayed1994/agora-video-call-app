<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgoraController;

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

//Route::get('/', [AgoraController::class, 'index']);
//Route::post('/token', [AgoraController::class, 'generateToken'])->name('generate.token');
//Route::get('/generate-token', [AgoraController::class, 'generateToken']);
//Route::post('/join', [AgoraController::class, 'joinCall']);

Route::middleware('auth')->group(function() {
    Route::get('/video-call', [AgoraController::class, 'index'])->name('video.call');
    Route::post('/generate-token', [AgoraController::class, 'generateToken'])->name('generate.token');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
