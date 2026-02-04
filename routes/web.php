<?php

use App\Http\Controllers\VehicleController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\TripTicketController;
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
Route::get('admin/driver', [DriverController::class, 'index'])->name('admin.driver');
Route::post('admin/driver', [DriverController::class, 'store'])->name('driver.store');
Route::put('admin/driver/{driver}', [DriverController::class, 'update'])->name('driver.update');
Route::delete('admin/driver/{driver}', [DriverController::class, 'destroy'])->name('driver.destroy');
Route::get('admin/vehicle', [VehicleController::class, 'index'])->name('admin.vehicle');
Route::post('admin/vehicle', [VehicleController::class, 'store'])->name('vehicle.store');
Route::put('admin/vehicle/{vehicle}', [VehicleController::class, 'update'])->name('vehicle.update');
Route::delete('admin/vehicle/{vehicle}', [VehicleController::class, 'destroy'])->name('vehicle.destroy');






Route::get('/client/home', function () {
    return view('client.home');
})->name('client.home');

Route::get('/client/booking', [BookingController::class, 'index'])->name('client.booking');

Route::get('/client/trip-ticket', function () {
    return view('client.trip-ticket');
})->name('client.trip-ticket');
Route::get('/trips', [TripTicketController::class, 'index'])->name('trips.index');
Route::post('/trips', [TripTicketController::class, 'store'])->name('trips.store');
Route::get('/trips/{tripTicket}', [TripTicketController::class, 'show'])->name('trips.show');
Route::put('/trips/{tripTicket}', [TripTicketController::class, 'update'])->name('trips.update');
Route::delete('/trips/{tripTicket}', [TripTicketController::class, 'destroy'])->name('trips.destroy');

Route::get('/client/travel-order', function () {
    return view('client.travel-order');
})->name('client.travel-order');

Route::get('/client/trip-history', function () {
    return view('client.trip-history');
})->name('client.trip-history');
