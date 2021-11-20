<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

#Import Class
use App\Http\Controllers\PatientsCovidController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/patients', [PatientsCovidController::class, 'index']);
Route::post('/patients', [PatientsCovidController::class, 'store']);
Route::get('/patients/{id}', [PatientsCovidController::class, 'show']);
Route::put('/patients/{id}', [PatientsCovidController::class, 'update']);
Route::delete('/patients/{id}', [PatientsCovidController::class, 'destroy']);

Route::get('/patients/search/{name}', [PatientsCovidController::class, 'search']);
Route::get('/patients/status/positive', [PatientsCovidController::class, 'positive']);
Route::get('/patients/status/recovered', [PatientsCovidController::class, 'recovered']);
Route::get('/patients/status/dead', [PatientsCovidController::class, 'dead']);
