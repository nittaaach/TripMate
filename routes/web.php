<?php

use App\Http\Controllers\Admin as Admin;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Customer as Customer;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — TripMate
|--------------------------------------------------------------------------
| Seluruh endpoint backend menggunakan web routes yang mengembalikan
| Blade views langsung. Saat template blade siap, tinggal dipanggil.
*/

// ==========================================
// Authentication Routes
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ==========================================
// Regional Indonesia Address API Routes (AJAX)
// ==========================================
Route::prefix('regional')->as('regional.')->group(function () {
    Route::get('/provinces', [\App\Http\Controllers\RegionalAddressController::class, 'provinces'])->name('provinces');
    Route::get('/regencies', [\App\Http\Controllers\RegionalAddressController::class, 'regencies'])->name('regencies');
    Route::get('/districts', [\App\Http\Controllers\RegionalAddressController::class, 'districts'])->name('districts');
    Route::get('/villages', [\App\Http\Controllers\RegionalAddressController::class, 'villages'])->name('villages');
});

// ==========================================
// Public Customer Routes
// ==========================================
Route::get('/', [Customer\HomeController::class, 'index'])->name('home');

Route::prefix('destinations')->as('customer.destinations.')->group(function () {
    Route::get('/', [Customer\DestinationController::class, 'index'])->name('index');
    Route::get('/{slug}', [Customer\DestinationController::class, 'show'])->name('show');
});

Route::prefix('vehicles')->as('customer.vehicles.')->group(function () {
    Route::get('/', [Customer\VehicleController::class, 'index'])->name('index');
    Route::get('/{vehicle}', [Customer\VehicleController::class, 'show'])->name('show');
});

// ==========================================
// Authenticated Customer Routes
// ==========================================
Route::middleware('auth')->as('customer.')->group(function () {
    // 1. Vacation Plans (Wizard Liburan & Rekomendasi)
    Route::prefix('vacation-plans')->as('vacation-plans.')->group(function () {
        Route::get('/', [Customer\VacationPlanController::class, 'index'])->name('index');
        Route::get('/create', [Customer\VacationPlanController::class, 'create'])->name('create');
        Route::post('/', [Customer\VacationPlanController::class, 'store'])->name('store');
        Route::get('/{vacationPlan}', [Customer\VacationPlanController::class, 'show'])->name('show');
        Route::delete('/{vacationPlan}', [Customer\VacationPlanController::class, 'destroy'])->name('destroy');

        // Recommendations sub-routes
        Route::get('/{vacationPlan}/recommendations', [Customer\RecommendationController::class, 'index'])->name('recommendations');
        Route::post('/{vacationPlan}/recommendations/regenerate', [Customer\RecommendationController::class, 'regenerate'])->name('recommendations.regenerate');
        Route::post('/{vacationPlan}/recommendations/{recommendation}/select', [Customer\RecommendationController::class, 'select'])->name('recommendations.select');
    });

    // 2. Bookings (Pemesanan & Riwayat)
    Route::prefix('bookings')->as('bookings.')->group(function () {
        Route::get('/', [Customer\BookingController::class, 'index'])->name('index');
        Route::get('/create', [Customer\BookingController::class, 'create'])->name('create');
        Route::post('/', [Customer\BookingController::class, 'store'])->name('store');
        Route::get('/{booking}', [Customer\BookingController::class, 'show'])->name('show');
        Route::post('/{booking}/cancel', [Customer\BookingController::class, 'cancel'])->name('cancel');

        // 3. Payment upload
        Route::get('/{booking}/payment', [Customer\PaymentController::class, 'show'])->name('payment');
        Route::post('/{booking}/payment', [Customer\PaymentController::class, 'store'])->name('payment.store');

        // 4. Review
        Route::get('/{booking}/review', [Customer\ReviewController::class, 'create'])->name('review.create');
        Route::post('/{booking}/review', [Customer\ReviewController::class, 'store'])->name('review.store');
    });
});

// ==========================================
// Staff & Admin Routes
// ==========================================
Route::prefix('admin')->as('admin.')->middleware(['auth', 'role:admin,staff'])->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Bookings management
    Route::prefix('bookings')->as('bookings.')->group(function () {
        Route::get('/', [Admin\BookingController::class, 'index'])->name('index');
        Route::get('/{booking}', [Admin\BookingController::class, 'show'])->name('show');
        Route::patch('/{booking}/status', [Admin\BookingController::class, 'updateStatus'])->name('update-status');
    });

    // Payments management & verification
    Route::prefix('payments')->as('payments.')->group(function () {
        Route::get('/', [Admin\PaymentController::class, 'index'])->name('index');
        Route::get('/{payment}', [Admin\PaymentController::class, 'show'])->name('show');
        Route::patch('/{payment}/verify', [Admin\PaymentController::class, 'verify'])->name('verify');
        Route::patch('/{payment}/reject', [Admin\PaymentController::class, 'reject'])->name('reject');
    });

    // Maintenance scheduling
    Route::resource('maintenance', Admin\MaintenanceController::class);

    // ==========================================
    // Admin Only: Fleet & Destination Management
    // ==========================================
    Route::middleware('role:admin')->group(function () {
        Route::resource('destinations', Admin\DestinationController::class)->except(['show']);
        Route::resource('vehicles', Admin\VehicleController::class)->except(['show']);
        Route::post('vehicles/{vehicle}/images', [Admin\VehicleController::class, 'storeImage'])->name('vehicles.images.store');
        Route::delete('vehicles/{vehicle}/images/{image}', [Admin\VehicleController::class, 'destroyImage'])->name('vehicles.images.destroy');
    });
});
