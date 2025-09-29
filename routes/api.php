<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
    Route::post('/logout',   [AuthController::class, 'logout'])
        ->middleware('auth:sanctum');
    Route::get('/me',   [AuthController::class, 'me'])
        ->middleware('auth:sanctum');
});


// ----------------------  Events ----------------------------

Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{id}', [EventController::class, 'show']);

// Protected organizer routes (must be authenticated and organizer)
Route::middleware(['auth:sanctum', 'role:organizer'])->group(function () {
    Route::post('/events', [EventController::class, 'store']);
    Route::put('/events/{id}', [EventController::class, 'update']);
    Route::delete('/events/{id}', [EventController::class, 'destroy']);
});


// ----------------------  Tickets ----------------------------

Route::middleware(['auth:sanctum', 'role:organizer'])->group(function () {
    Route::post('/events/{event}/tickets', [TicketController::class, 'store']);
    Route::put('/tickets/{ticket}', [TicketController::class, 'update']);
    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy']);
});


// ----------------------  Booking ----------------------------

Route::middleware(['auth:sanctum', 'role:customer'])->group(function () {
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::put('/bookings/{booking}/cancel', [BookingController::class, 'cancel']);
});
Route::middleware(['auth:sanctum', 'role:customer', 'prevent.double.booking'])
    ->post('/tickets/{ticket}/bookings', [BookingController::class, 'store']);

// ----------------------  Payment ----------------------------

Route::middleware(['auth:sanctum', 'role:customer'])->group(function () {
    Route::post('/bookings/{booking}/payment', [PaymentController::class, 'pay']);
    Route::get('/payments/{payment}', [PaymentController::class, 'show']);
});






// ---------- Role Examples : Testing middleware ----------

// Only Admin can manage all events
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', fn () =>
    response()->json(['message' => 'Welcome Admin'])
    );
});

// Organizers manage their own events
Route::middleware(['auth:sanctum', 'role:organizer'])->group(function () {
    Route::get('/organizer/events', fn () =>
    response()->json(['message' => 'Organizer area'])
    );
});

// Customers can book tickets
Route::middleware(['auth:sanctum', 'role:customer'])->group(function () {
    Route::get('/customer/bookings', fn () =>
    response()->json(['message' => 'Customer bookings'])
    );
});

// Mixed roles allowed (Admin OR Organizer)
Route::middleware(['auth:sanctum', 'role:admin,organizer'])->group(function () {
    Route::get('/shared/manage', fn () =>
    response()->json(['message' => 'Admin & Organizer shared area'])
    );
});
