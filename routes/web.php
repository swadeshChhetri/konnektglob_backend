<?php

use Illuminate\Support\Facades\Route;


Route::get('/link', function(){
    Artisan::call('storage:link');
});

Route::get('/', function () {
    return view('welcome');
});
