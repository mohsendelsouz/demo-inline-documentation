<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web api for your application. These
| api are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    \App\Jobs\WeeklyGoalCalculation::dispatch();
//     \App\Jobs\WeeklyGoalCreate::dispatch();
    return view('welcome');
});
