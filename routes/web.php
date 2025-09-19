<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\CustomVerificationController;
use App\Http\Controllers\Api\CheckoutController;

/*
|--------------------------------------------------------------------------
| Guest & Landing Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('admin.dashboard');
    }
    return view('admin.welcome');
});

// Email verification
Route::get('/email/verify/{token}', [CustomVerificationController::class, 'verify'])
    ->name('verification.verify.token');

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthenticationController::class, 'registerForm'])->name('registerForm');
    Route::post('/register', [AuthenticationController::class, 'register'])->name('register');

    Route::get('/login', [AuthenticationController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthenticationController::class, 'login'])->name('login.post');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:Dashboard')
        ->name('admin.dashboard');

    // Logout
    Route::post('/logout', [AuthenticationController::class, 'logout'])->name('logout');

    // Categories
    Route::get('categories/export', [CategoryController::class, 'export'])->name('categories.export');
    Route::post('categories/import', [CategoryController::class, 'import'])->name('categories.import');
    Route::get('categories/template', [CategoryController::class, 'downloadTemplate'])->name('categories.template');
    Route::resource('categories', CategoryController::class)->middleware('permission:Categories');

    // Products
    Route::get('/products/template', [ProductController::class, 'downloadTemplate'])->name('products.template');
    Route::get('/products/export', [ProductController::class, 'export'])->name('products.export');
    Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');
    Route::resource('products', ProductController::class)->middleware('permission:Product');

    // Users
    Route::get('users', [UserController::class, 'index'])
        ->middleware('permission:Users')
        ->name('users.index');
    Route::get('users/{id}', [UserController::class, 'show'])
        ->middleware('permission:Users')
        ->name('users.show');
    Route::get('/export/user', [UserController::class, 'export'])
        ->middleware('permission:Users')
        ->name('export.user');
    Route::get('/users/{id}/export-orders', [UserController::class, 'exportUserOrders'])
        ->middleware('permission:Users')
        ->name('users.exportOrders');

    // Orders
    Route::get('orders', [OrderController::class, 'index'])
        ->middleware('permission:Orders')
        ->name('orders.index');
    Route::get('orders/{id}', [OrderController::class, 'show'])
        ->middleware('permission:Orders')
        ->name('orders.show');
    Route::get('orders/{id}/invoice', [OrderController::class, 'downloadInvoice'])
        ->middleware('permission:Orders')
        ->name('orders.invoice');
    Route::get('/export/order', [OrderController::class, 'export'])
        ->middleware('permission:Orders')
        ->name('export.order');
    Route::put('/admin/orders/{order}/status', [OrderController::class, 'updateStatus'])
        ->middleware('permission:Orders')
        ->name('orders.updateStatus');

    // Settings
    Route::prefix('settings')->middleware('permission:Setting')->group(function () {
        Route::get('/', [AuthenticationController::class, 'settingView'])->name('settings.index');
        Route::post('/email', [AuthenticationController::class, 'requestEmailChange'])->name('settings.email.change');
        Route::get('/check-old-email', function (Request $request) {
            return response()->json([
                'valid' => $request->email === Auth::user()->email
            ]);
        })->name('settings.checkOldEmail');
    });

    // Notifications → kalau perlu bisa bikin permission "Notifications"
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/{id}', [NotificationController::class, 'show'])->name('notifications.show');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::delete('/', [NotificationController::class, 'clear'])->name('notifications.clear');
    });

    // Roles
    Route::resource('roles', RoleController::class)
        ->middleware('permission:Role');

    // Admins
    Route::prefix('admins')->middleware('permission:Admins')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('admins.index');
        Route::get('/export', [AdminController::class, 'export'])->name('admins.export'); // <-- Tambah ini
        Route::get('/create', [AdminController::class, 'create'])->name('admins.create');
        Route::post('/', [AdminController::class, 'store'])->name('admins.store');
        Route::get('/{id}/edit', [AdminController::class, 'edit'])->name('admins.edit');
        Route::put('/{id}', [AdminController::class, 'update'])->name('admins.update');
        Route::delete('/{id}', [AdminController::class, 'destroy'])->name('admins.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Misc
|--------------------------------------------------------------------------
*/
Route::get('/greeting/{locale}', function (string $locale) {
    if (! in_array($locale, ['en', 'id'])) {
        abort(400);
    }
    App::setLocale($locale);
    session()->put('locale', $locale);
    return back();
})->name('set.language');

// Checkout
Route::get('/checkout-page/{orderId}', [CheckoutController::class, 'showCheckoutPage'])
    ->name('checkout.page');