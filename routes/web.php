<?php

use App\Http\Controllers\SensorDataController;
use App\Models\SensorData;
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

Route::get('/', function () {
    $data = SensorData::latest()->take(10)->get();
    return view('dashboard', compact('data'));
});
Route::get('/dashboard', [SensorDataController::class, 'showDashboard']);