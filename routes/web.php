<?php

use Illuminate\Support\Facades\Route;

Route::get('{any?}', function () {
  return view('application');
})
  ->where('any', '^(?!api).*$') //ANCHOR - Exclude api routes.
  ->name('fallback');
