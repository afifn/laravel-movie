<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MovieController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\Member\LoginController;
use App\Http\Controllers\Member\MovieController as MemberMovieController;
use App\Http\Controllers\Member\PricingController;
use App\Http\Controllers\Member\RegisterController;
use App\Http\Controllers\Member\TransactionController as MemberTransactionController;
use App\Http\Controllers\Member\WebhookController;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::view('/', 'index')->name('index');
Route::get('/pricing', [PricingController::class, 'index'])->name('member.pricing');

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register',  'index')->name('member.register');
    Route::post('/register', 'store')->name('member.register.store');
});

Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'index')->name('member.login');
    Route::post('/login', 'authenticate')->name('member.login.auth');
    Route::get('/logout', 'logout')->name('member.logout');
});

/* controller member */
Route::controller(MemberDashboardController::class)->prefix('dashboard')->middleware('auth')->group(function () {
    Route::get('/',  'index')->name('member.dashboard');
    Route::get('/subscription', 'subscription')->name('member.subscription');

    Route::controller(MemberMovieController::class)->prefix('movie')->group(function () {
        Route::get('/{id}', 'show')->name('member.movie.show');
        Route::get('/{id}/watch', 'watch')->name('member.movie.watch');
    });
    Route::post('transaction', [MemberTransactionController::class, 'store'])->name('member.transaction.store');
});

Route::view('/payment-success', 'member.payment-success');
Route::post('/payment-notification', [WebhookController::class, 'handler'])
    ->withoutMiddleware(VerifyCsrfToken::class);

/* panggil controller admin */
Route::prefix('admin')->middleware('admin.auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::controller(MovieController::class)->prefix('movie')->group(function () {
        Route::get('/', 'index')->name('admin.movies');

        Route::get('/create', 'create')->name('admin.movie.create');
        Route::post('/store', 'store')->name('admin.movie.store');

        Route::get('/edit/{id}', 'edit')->name('admin.movie.edit');
        Route::put('/update/{id}', 'update')->name('admin.movie.update');

        Route::delete('/delete/{id}', 'delete')->name('admin.movie.delete');
    });

    Route::get('/transaction', [TransactionController::class, 'index'])->name('admin.transaction');

    Route::controller(NotificationController::class)->prefix('notification')->group(function () {
        Route::get('/', 'index')->name('admin.notification');
        Route::post('/store', 'store')->name('admin.notification.store');
    });

    Route::get('/logout', [AuthController::class, 'logout'])->name('admin.logout');
});

Route::controller(AuthController::class)->prefix('admin')->group(function () {
    Route::get('/login', 'index')->name('admin.login');
    Route::post('/login', 'authenticate')->name('admin.auth');
});
