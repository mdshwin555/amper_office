<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrintBillController;


Route::get('/', function () {
    return view('welcome');
});


Route::get('/print-bills', [PrintBillController::class, 'index'])->name('print.bills');
