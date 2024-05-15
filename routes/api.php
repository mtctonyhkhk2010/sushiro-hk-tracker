<?php

use App\Models\PushSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');

Route::post("push-subscribe", function(Request $request) {
//    dd($request->all());
    PushSubscription::updateOrCreate(
        [
            'session_id' => $request->session_id
        ],
        [
            'store_id' => $request->store_id,
            'queue_number' => $request->queue_number,
            'data' => json_encode($request->data),
            'done' => false,
        ]
    );
});
