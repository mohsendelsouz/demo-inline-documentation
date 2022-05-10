<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API api for your application. These
| api are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('manager')->group(function () {
    include 'api/manager.php';
});

Route::prefix('technician')->group(function () {
    include 'api/technician.php';
});
