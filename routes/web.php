<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\ProfileController;
use App\Models\Product;

// ================================
// Public Routes (no auth required)
// ================================
Route::get('/', fn() => view('home'))->name('home');

Route::get('/products', fn() => view('products', [
    'products' => Product::where('is_available', true)->get(),
]))->name('products');

Route::get('/cart', fn() => view('cart'))->name('cart');

Route::get('/checkout', fn() => view('checkout'))->name('checkout');

Route::get('/success', fn() => view('success'))->name('success');

Route::get('/orders', fn() => view('orders'))->name('my-orders');

Route::get('/contact', fn() => view('contact'))->name('contact');



// Secret registration routes (public)
Route::get('/secret-admin-register', fn() => view('auth.register', ['role' => 'admin']));
Route::get('/secret-staff-register', fn() => view('auth.register', ['role' => 'staff']));

// ================================
// Authentication Routes
// ================================
require __DIR__.'/auth.php';

// ================================
// Authenticated Routes
// ================================
Route::middleware('auth')->group(function () {
    // Default Laravel dashboard
    Route::get('/dashboard', fn() => view('dashboard', [
        'orders' => Auth::user()->role === 'admin' ? App\Models\Order::latest()->get() : collect()
    ]))
        ->middleware('verified')
        ->name('dashboard');

    // Profile routes (Laravel default)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Role-specific dashboards (protected by role middleware)
    Route::middleware('role:admin')->get('/admin', function () {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized. Admin access only.');
        }
        $orders = App\Models\Order::latest()->get();
        return view('admin.dashboard', compact('orders'));
    })->middleware(['auth', 'verified'])->name('admin.dashboard');

    Route::middleware('role:staff')->get('/staff/dashboard', function () {
        $orders = App\Models\Order::where('status', 'assigned')
            ->whereHas('assignment', fn($q) => $q->where('staff_id', auth()->id()))
            ->get();

        return view('staff.dashboard', compact('orders'));
    })->name('staff.dashboard');
});

// ================================
// Order Placement (POST route - should be protected)
// ================================
Route::post('/orders', [OrderController::class, 'store'])
    ->middleware('auth') // Only logged-in customers can place orders
    ->name('orders.store');

// Add this with public routes
Route::get('/register', function () {
    return view('auth.register', ['role' => 'customer']);
})->name('register');

// Additional authenticated routes
Route::middleware('auth')->group(function () {
    // Staff routes
    Route::post('/orders/{order}/start-journey', [OrderController::class, 'startJourney'])->middleware('role:staff')->name('orders.start-journey');
    Route::post('/orders/{order}/mark-delivered', [OrderController::class, 'markDelivered'])->middleware('role:staff')->name('orders.mark-delivered');
    Route::post('/staff/toggle-availability', [OrderController::class, 'toggleAvailability'])->middleware('role:staff')->name('staff.toggle-availability');

    // Customer routes
    Route::post('/orders/{order}/customer-confirm-delivery', [OrderController::class, 'customerConfirmDelivery'])->middleware('role:customer')->name('orders.customer-confirm-delivery');

    // Admin routes
    Route::delete('/orders/{order}', [OrderController::class, 'cancel'])->middleware('role:admin')->name('orders.cancel');
});

   
// In web.php, add logout route if needed (Laravel has it, but to redirect to login)
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');    
// Include Laravel authentication routes
require __DIR__.'/auth.php';