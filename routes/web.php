<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\OrderController; // If you have this controller
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
    Route::get('/dashboard', fn() => view('dashboard'))
        ->middleware('verified')
        ->name('dashboard');

    // Profile routes (Laravel default)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Role-specific dashboards (protected by role middleware)
    Route::middleware('role:admin')->get('/admin', function () {
        // Add admin data here later (e.g. $orders = Order::latest()->get())
        return view('admin.dashboard');
    })->name('admin.dashboard');

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

    
// In web.php, add logout route if needed (Laravel has it, but to redirect to login)
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');    
// Include Laravel authentication routes
require __DIR__.'/auth.php';