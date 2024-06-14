<?php

use App\Livewire\Welcome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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


    if (Cache::has('access_token'))
    {
        $access_token = Cache::get('access_token');
    }
    else
    {
        $response = \Illuminate\Support\Facades\Http::asForm()->acceptJson()->
        withHeaders([
            'Authorization' => 'Basic ZUxIcVl1QVM3bWtNdlU5bkZGV3JOS1NucmhsbUhta3BzV3BXR2VJSUlyUW9pWFEyOjJjeDlHNVJBcTBmdVdHYjNiYzhBUk1RUXhtclZDbzlHdEZhMkV3aTBYQ3dBZUVSU1RYODE2djU4WmVCWVViU0s='
        ])
            ->post('https://developer.api.autodesk.com/authentication/v2/token', [
                'grant_type' => 'client_credentials',
                'scope'      => 'code:all data:write data:read bucket:create bucket:delete bucket:read'
            ])->collect();

        $access_token = $response['access_token'];

        Cache::put('access_token', $response['access_token'], $response['expires_in'] - 30);
    }

    return view('test', compact('access_token'));
});
