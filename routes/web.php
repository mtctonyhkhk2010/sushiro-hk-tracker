<?php

use App\Livewire\Welcome;
use Illuminate\Http\Request;
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

Route::get('/', \App\Livewire\Tracker::class);
Route::get('/store/{store}', \App\Livewire\ShowStore::class);
Route::get('/statistic', \App\Livewire\ShowStatistic::class);
Route::get('/test3d', function (Request $request) {
    return view('test');
});
