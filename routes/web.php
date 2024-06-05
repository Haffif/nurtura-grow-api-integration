<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AntaresController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LahanController;
use App\Http\Controllers\PenanamanController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\TanamanController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\MachineLearningController;
use App\Http\Controllers\PengairanController;

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
    return response()->json(["message" => "hello world"]);
});

Route::group([
    'prefix' => 'antares',
    'as' => 'antares.'
], function () {
    Route::group([
        'prefix' => 'webhook',
        'as' => 'webhook.'
    ], function () {
        Route::post('camera', [AntaresController::class, 'handleAntaresCamera'])->name('camera');
        Route::post('sensor', [AntaresController::class, 'handleAntaresSensor'])->name('sensor');
    });
    Route::post('downlink', [AntaresController::class, 'handleAntaresDownlink'])->name('downlink');
});

Route::group([
    'prefix' => 'ml',
    'as' => 'ml.'
], function () {
    Route::post('irrigation', [MachineLearningController::class, 'irrigation'])->name('irrigation');
    Route::post('predict', [MachineLearningController::class, 'predict'])->name('predict');
});

Route::middleware('guest')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

Route::middleware('jwt.verify')->group(function () {
    Route::group(['prefix' => 'fertilizer'], function () {
        Route::get('data');
        Route::post('input'); // input data air untuk melakukan penjadwalan siram air
        Route::group([
            'prefix' => 'sop',
            'as' => 'sop.'
        ], function () {
            Route::post('input');
        });
    });
    Route::group(['prefix' => 'irrigation'], function () {
        Route::get('data');
        Route::post('input'); 
        Route::group([
            'prefix' => 'sop',
            'as' => 'sop.'
        ], function () {
            Route::post('input', [PengairanController::class, 'input_sop']);
        });
    });
    Route::group(['prefix' => 'sensor'], function () {
        Route::get('data', [SensorController::class, 'get_sensor']);
    });
    Route::group(['prefix' => 'plant'], function () {
        Route::get('data', [TanamanController::class, 'get_plant']);
    });
    Route::group(['prefix' => 'device'], function () {
        Route::get('data', [DeviceController::class, 'get_device']);
        Route::post('input', [DeviceController::class, 'input_device']);
    });
    Route::group(['prefix' => 'penanaman'], function () {
        Route::get('data', [PenanamanController::class, 'get_penanaman']); // get data penanaman by user id
        Route::post('input', [PenanamanController::class, 'input_penanaman']); // tambah data penanaman
        Route::put('tinggi', [PenanamanController::class, 'update_tinggi']); // input manual atau update data penanaman
    });
    Route::group(['prefix' => 'lahan'], function () {
        Route::get('data', [LahanController::class, 'get_lahan']); // get data lahan by user id
        Route::post('input', [LahanController::class, 'input_lahan']); // tambah data lahan
    });
    Route::post('refresh-token', [AuthController::class, 'refresh']);
    Route::post('logout', [AuthController::class, 'logout']);
});
