<?php

use Illuminate\Support\Facades\Route;

use App\Models\Shop;
use App\Models\User;
use App\Models\Service;
use App\Models\Appointment;

Route::get('/', function () {
    $barbers = \App\Models\User::where('role', \App\Models\User::ROLE_BARBER)
        ->orWhere('show_in_gallery', true)
        ->get(['id', 'name', 'avatar_url', 'specialty'])
        ->map(function ($user) {
            $user->avatar_url = $user->avatar_url ? Storage::url($user->avatar_url) : "https://i.pravatar.cc/400?u={$user->id}";
            return $user;
        });
    
    $translations = __('gallery');
    
    return view('welcome', [
        'barbers' => $barbers,
        'translations' => $translations,
        'locale' => app()->getLocale(),
    ]);
});

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['ro', 'hu'])) {
        session(['locale' => $locale]);
    }
    return back();
});


Route::get('/login', function () {
    if (Auth::check()) {
        return redirect('/admin');
    }
    return view('login');
})->name('login');

Route::post('/login', function (Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        return redirect('/admin');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
});

Route::get('/book', function () {
    $barbers = \App\Models\User::where('role', \App\Models\User::ROLE_BARBER)
        ->orWhere('show_in_gallery', true)
        ->get(['id', 'name', 'avatar_url'])
        ->map(function ($user) {
            $user->avatar_url = $user->avatar_url ? Storage::url($user->avatar_url) : "https://i.pravatar.cc/400?u={$user->id}";
            return $user;
        });
    $services = \App\Models\Service::all(['id', 'name', 'price', 'duration_minutes']);
    $appointments = \App\Models\Appointment::where('start_at', '>=', now()->startOfDay())
        ->get(['user_id', 'start_at'])
        ->map(function ($app) {
            return [
                'user_id' => $app->user_id,
                'date' => \Carbon\Carbon::parse($app->start_at)->format('Y-m-d'),
                'time' => \Carbon\Carbon::parse($app->start_at)->format('H:i'),
            ];
        });
    
    return view('booking', [
        'barbers' => $barbers,
        'services' => $services,
        'appointments' => $appointments,
        'translations' => __('booking'),
        'locale' => app()->getLocale(),
    ]);
});


Route::post('/book', function (Illuminate\Http\Request $request) {
    $data = $request->validate([
        'user_id' => 'required|exists:users,id',
        'customer_name' => 'required|string|max:255',
        'service' => 'required|string|max:255',
        'start_at' => 'required|date|after:now',
    ]);

    \App\Models\Appointment::create($data);

    return back()->with('success', 'Your appointment has been booked successfully!');
});


Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Placeholder for other admin routes
    Route::get('/appointments', [App\Http\Controllers\AdminController::class, 'appointments'])->name('admin.appointments');
    
    Route::get('/barbers', [App\Http\Controllers\AdminController::class, 'barbers'])->name('admin.barbers');
    Route::post('/barbers', [App\Http\Controllers\AdminController::class, 'storeBarber'])->name('admin.barbers.store');
    Route::post('/barbers/{barber}', [App\Http\Controllers\AdminController::class, 'updateBarber'])->name('admin.barbers.update');
    
    Route::post('/profile', [App\Http\Controllers\AdminController::class, 'updateProfile'])->name('admin.profile.update');
    
    Route::post('/services', [App\Http\Controllers\AdminController::class, 'storeService'])->name('admin.services.store');
    Route::delete('/services/{service}', [App\Http\Controllers\AdminController::class, 'deleteService'])->name('admin.services.delete');

    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});
