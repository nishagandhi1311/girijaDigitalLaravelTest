<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('dynamicTimeTableForm');
})->name('dynamicTimeTableForm');
Route::get('dynamicTimeTableStubjectForm', function () {
    return view('dynamicTimeTableStubjectForm');
})->name('dynamicTimeTableStubjectForm');
Route::get('TimeTable', function () {
    return view('TimeTable');
})->name('TimeTable');
Route::post('DynamicTimeTableController/CalculateHoursForWeek', [App\Http\Controllers\DynamicTimeTableController::class, 'CalculateHoursForWeek'])->name('CalculateHoursForWeek');
Route::post('DynamicTimeTableController/CreateTimeTable', [App\Http\Controllers\DynamicTimeTableController::class, 'CreateTimeTable'])->name('CreateTimeTable');

