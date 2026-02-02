<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('portal');
});


Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');
Route::get('/admin/booking', function () {
    return view('admin.booking');
})->name('admin.booking');
Route::get('/admin/driver', function () {
    return view('admin.driver');
})->name('admin.driver');
Route::get('/admin/vehicle', function () {
    return view('admin.vehicle');
})->name('admin.vehicle');






Route::get('/client/home', function () {
    return view('client.home');
})->name('client.home');
Route::get('/client/history', function () {
    return view('client.history');
})->name('client.history');
Route::get('/client/booking', function () {
    return view('client.booking');
})->name('client.booking');
