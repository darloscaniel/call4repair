<?php

use Illuminate\Support\Facades\Route;

Route::get('/{any}', function () {
    return view('index'); // Essa é a blade que carrega o Vue
})->where('any', '^(?!api|up).*$');

