<?php

use App\Http\Controllers\VehicleController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TripTicketController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TravelOrderController;
use App\Http\Controllers\ToReportController;
use App\Http\Controllers\NationalToController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    return view('portal');
});

// Guest Only
Route::middleware('guest')->group(function () {
    Route::get('/admin-login', function () {
        return view('auth.admin-login');
    })->name('admin.login');
    Route::get('/client-login', function () {
        return view('auth.client-login');
    })->name('client.login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('client.password.request');
    Route::post('/forgot-password', [AuthController::class, 'resetPassword'])->name('client.password.reset.verify');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- ADMIN PROTECTED ROUTES ---
Route::middleware(['auth:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/booking', [TripTicketController::class, 'indexAdmin'])->name('admin.booking');
    Route::get('/booking/export', [TripTicketController::class, 'exportExcel'])->name('admin.booking.export');
    Route::get('/booking/{tripTicket}', [TripTicketController::class, 'showAdmin'])->name('admin.booking.show');
    Route::put('/booking/{tripTicket}', [TripTicketController::class, 'update'])->name('admin.booking.update');


    Route::get('/book', [TripTicketController::class, 'adminBook'])->name('admin.book');

    Route::post('/book', [TripTicketController::class, 'storeBooking'])
        ->name('book.store');
    Route::delete('/admin/bookings/{ticket}', [TripTicketController::class, 'destroyBooking'])
        ->name('admin.booking.destroy');



    Route::get('/driver', [DriverController::class, 'index'])->name('admin.driver');
    Route::post('/driver', [DriverController::class, 'store'])->name('driver.store');
    Route::put('/driver/{driver}', [DriverController::class, 'update'])->name('driver.update');
    Route::delete('/driver/{driver}', [DriverController::class, 'destroy'])->name('driver.destroy');

    Route::get('/vehicle', [VehicleController::class, 'index'])->name('admin.vehicle');
    Route::post('/vehicle', [VehicleController::class, 'store'])->name('vehicle.store');
    Route::put('/vehicle/{vehicle}', [VehicleController::class, 'update'])->name('vehicle.update');
    Route::delete('/vehicle/{vehicle}', [VehicleController::class, 'destroy'])->name('vehicle.destroy');



    Route::put('/admin/booking/{tripTicket}/assign-driver', [TripTicketController::class, 'assignDriver'])->name('admin.booking.assign');
    Route::put('/admin/booking/{tripTicket}/update-status', [TripTicketController::class, 'updateStatus'])->name('admin.booking.status');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('admin.notification');
    Route::patch('/notifications/{notification}', [NotificationController::class, 'update'])->name('admin.notification.update');
    Route::get('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('admin.notification.read');
    Route::post('/admin/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('admin.notifications.markAllRead');
    Route::delete('/admin/notifications/clear-all', [NotificationController::class, 'clearAll'])->name('admin.notifications.clearAll');
});

// --- CLIENT PROTECTED ROUTES ---
Route::middleware(['auth:client'])->prefix('client')->group(function () {

    Route::get('/tt-print', function () {
        return view('client.tt-print');
    })->name('client.tt-print');
    Route::get('/to-print', function () {
        return view('client.to-print');
    })->name('client.to-print');


    Route::get('/profile', function () {
        return view('client.profile');
    })->name('client.profile');
    Route::get('/profile', [ClientController::class, 'profile'])->name('client.profile');
    Route::patch('/profile', [ClientController::class, 'updateProfile'])->name('client.profile.update');






    Route::get('/home', [ClientController::class, 'index'])->name('client.home');
    Route::get('/booking', [TripTicketController::class, 'index'])->name('client.booking');
    Route::post('/booking', [TripTicketController::class, 'store'])->name('trips.store');
    Route::get('/booking/{tripTicket}', [TripTicketController::class, 'show'])->name('trips.show');
    Route::delete('/booking/{tripTicket}', [TripTicketController::class, 'destroy'])->name('client.booking.destroy');

    Route::get('/trip-ticket', [TripTicketController::class, 'tripTicket'])->name('client.trip-ticket');
    Route::get('/trip-ticket/{tripTicket}/ticket', [TripTicketController::class, 'showTicket'])->name('client.trip-ticket.ticket');
    Route::patch('/trip-ticket/{tripTicket}/add-info', [TripTicketController::class, 'addInfo'])
        ->name('client.trip-ticket.add-info');
    Route::post('/trip-tickets/{tripTicket}/generate-to', [TravelOrderController::class, 'generateTo'])
        ->name('trip-tickets.generate-to');



    Route::get('/trip-ticket/travel-order/{tripTicket}', [TravelOrderController::class, 'show'])
        ->name('client.trip-ticket.travel-order.show');
    Route::patch('/trip-ticket/travel-order/{tripTicket}', [TravelOrderController::class, 'store'])
        ->name('client.trip-ticket.travel-order.store');
    Route::post('/trip-ticket/travel-order/{tripTicket}/track', [TravelOrderController::class, 'track'])
        ->name('client.trip-ticket.travel-order.track');

    Route::post('/notifications/{notification}/read', [NotificationController::class, 'clientRead'])->name('client.notification.read');

    Route::get('/document-tracking', [TravelOrderController::class, 'index'])->name('client.document-tracking');
    Route::get('/document-tracking/{tripTicket}', [TravelOrderController::class, 'showTracking'])->name('client.document-tracking.show');
    Route::post('/document-tracking/{tripTicket}/track', [TravelOrderController::class, 'track'])->name('client.document-tracking.track');
    Route::post('/document-tracking/{tripTicket}/receive', [TravelOrderController::class, 'receive'])->name('client.document-tracking.receive');
    Route::delete('/document-tracking/{id}', [TravelOrderController::class, 'destroy'])->name('client.document-tracking.destroy');

    Route::get('/to-report', [ToReportController::class, 'index'])->name('client.to-report');
    Route::post('/to-report', [ToReportController::class, 'store'])->name('to-report.store');
    Route::put('/to-report/{id}', [ToReportController::class, 'update'])->name('to-report.update');
    Route::delete('/to-report/{id}', [ToReportController::class, 'destroy'])->name('to-report.destroy');
    Route::get('/to-report/export', [ToReportController::class, 'export'])->name('to-report.export');


    Route::get('/national-to', [NationalToController::class, 'index'])->name('client.national-to');
    Route::post('/national-to', [NationalToController::class, 'store'])->name('client.national-to.store');
    Route::get('/national-to/{id}', [NationalToController::class, 'show'])->name('client.national-to.show');
    Route::put('/national-to/{id}', [NationalToController::class, 'update'])->name('client.national-to.update');
    Route::delete('/national-to/{id}', [NationalToController::class, 'destroy'])->name('client.national-to.destroy');

    Route::get('/trip-history', function () {
        return view('client.trip-history');
    })->name('client.trip-history');
});
