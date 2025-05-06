<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\SensorData;
use App\Http\Controllers\SensorDataController;

/*
|--------------------------------------------------------------------------  
| API Routes  
|--------------------------------------------------------------------------  
|  
| Here is where you can register API routes for your application. These  
| routes are loaded by the RouteServiceProvider and all of them will  
| be assigned to the "api" middleware group. Make something great!  
|  
*/

/* // Route to retrieve user data (as an example, using sanctum authentication)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
 */
Route::apiResource('sensor-data', SensorDataController::class);
// Route to store sensor data (POST method)
Route::post('sensor-data', [SensorDataController::class, 'store']);


// Route to retrieve all sensor data (GET method)
// Route::get('/sensor-data', [SensorDataController::class, 'index']);

