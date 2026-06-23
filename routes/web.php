<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentNotification;
use App\Mail\ClientBookingConfirmation;

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
        return Auth::user()->role === \App\Models\User::ROLE_CUSTOMER ? redirect('/client') : redirect('/admin');
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
        return Auth::user()->role === \App\Models\User::ROLE_CUSTOMER ? redirect('/client') : redirect('/admin');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
});

Route::post('/logout', function (Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/client', function () {
        if (Auth::user()->role !== \App\Models\User::ROLE_CUSTOMER) {
            return redirect('/admin');
        }

        $appointments = \App\Models\Appointment::with('barber')
            ->where('customer_email', Auth::user()->email)
            ->orderBy('start_at', 'desc')
            ->get();

        return view('client.dashboard', [
            'appointments' => $appointments,
            'user' => Auth::user(),
        ]);
    });
});
Route::get('/book', function () {
    $barbers = \App\Models\User::where('role', \App\Models\User::ROLE_BARBER)
        ->orWhere('show_in_gallery', true)
        ->with(['schedules', 'daysOff'])
        ->get(['id', 'name', 'avatar_url'])
        ->map(function ($user) {
            $user->avatar_url = $user->avatar_url ? Storage::url($user->avatar_url) : "https://i.pravatar.cc/400?u={$user->id}";
            return $user;
        });
    $services = \App\Models\Service::all(['id', 'name', 'category', 'price', 'duration_minutes']);
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


Route::get('/politica-de-confidentialitate', function () {
    return view('privacy-policy', [
        'locale' => app()->getLocale(),
    ]);
});

Route::post('/book', function (Illuminate\Http\Request $request) {
    $data = $request->validate([
        'user_id' => 'required|exists:users,id',
        'customer_name' => 'required|string|max:255',
        'customer_phone' => 'required|string|min:10|max:255',
        'customer_email' => 'nullable|email|max:255',
        'service' => 'required|string|max:255',
        'start_at' => 'required|date|after:now',
        'privacy_policy' => 'accepted',
    ], [
        'privacy_policy.accepted' => __('booking.validation_privacy_policy'),
    ]);

    $startAt = \Carbon\Carbon::parse($data['start_at']);
    $barber = \App\Models\User::findOrFail($data['user_id']);

    // Check Day Off
    $hasDayOff = $barber->daysOff()->whereDate('date', $startAt->toDateString())->exists();
    if ($hasDayOff) {
        return back()->withErrors(['start_at' => __('booking.validation_day_off')])->withInput();
    }

    // Check Weekday Schedule
    $dayOfWeek = $startAt->dayOfWeek; // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
    $schedule = $barber->schedules()->where('day_of_week', $dayOfWeek)->first();
    if (!$schedule || !$schedule->is_working) {
        return back()->withErrors(['start_at' => __('booking.validation_not_working_day')])->withInput();
    }

    $timeStr = $startAt->format('H:i:s');
    if ($timeStr < $schedule->start_time || $timeStr >= $schedule->end_time) {
        return back()->withErrors(['start_at' => __('booking.validation_out_of_hours')])->withInput();
    }

    // Unset privacy_policy before creating the appointment if it's not in the database table
    unset($data['privacy_policy']);
    $appointment = \App\Models\Appointment::create($data);

    try {
        Mail::to($barber->email)->send(new AppointmentNotification($appointment, 'booked'));
        
        if (!empty($appointment->customer_email)) {
            $generatedPassword = null;
            $user = \App\Models\User::where('email', $appointment->customer_email)->first();
            
            if (!$user) {
                $generatedPassword = \Illuminate\Support\Str::random(10);
                \App\Models\User::create([
                    'name' => $appointment->customer_name,
                    'email' => $appointment->customer_email,
                    'phone' => $appointment->customer_phone,
                    'password' => \Illuminate\Support\Facades\Hash::make($generatedPassword),
                    'role' => \App\Models\User::ROLE_CUSTOMER,
                ]);
            }

            Mail::to($appointment->customer_email)->send(new ClientBookingConfirmation($appointment, $generatedPassword));
        }
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Failed to send booking email: ' . $e->getMessage());
    }

    return back()->with('success', 'Your appointment has been booked successfully!');
});


Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');

    Route::get('/appointments', [App\Http\Controllers\AdminController::class, 'appointments'])->name('admin.appointments');
    Route::post('/appointments', [App\Http\Controllers\AdminController::class, 'storeAppointment'])->name('admin.appointments.store');
    Route::post('/appointments/{appointment}', [App\Http\Controllers\AdminController::class, 'updateAppointment'])->name('admin.appointments.update');
    Route::delete('/appointments/{appointment}', [App\Http\Controllers\AdminController::class, 'deleteAppointment'])->name('admin.appointments.delete');

    Route::get('/barbers', [App\Http\Controllers\AdminController::class, 'barbers'])->name('admin.barbers');
    Route::post('/barbers', [App\Http\Controllers\AdminController::class, 'storeBarber'])->name('admin.barbers.store');
    Route::post('/barbers/{barber}', [App\Http\Controllers\AdminController::class, 'updateBarber'])->name('admin.barbers.update');

    // New schedule routes
    Route::get('/my-schedule', [App\Http\Controllers\AdminController::class, 'mySchedule'])->name('admin.my-schedule');
    Route::get('/barbers/{barber}/schedule', [App\Http\Controllers\AdminController::class, 'getSchedule'])->name('admin.barbers.schedule');
    Route::post('/barbers/{barber}/schedule', [App\Http\Controllers\AdminController::class, 'updateSchedule'])->name('admin.barbers.schedule.update');
    Route::post('/barbers/{barber}/days-off', [App\Http\Controllers\AdminController::class, 'addDayOff'])->name('admin.barbers.days-off.store');
    Route::delete('/barbers/{barber}/days-off/{dayOff}', [App\Http\Controllers\AdminController::class, 'deleteDayOff'])->name('admin.barbers.days-off.delete');

    Route::post('/profile', [App\Http\Controllers\AdminController::class, 'updateProfile'])->name('admin.profile.update');


    Route::post('/services', [App\Http\Controllers\AdminController::class, 'storeService'])->name('admin.services.store');
    Route::delete('/services/{service}', [App\Http\Controllers\AdminController::class, 'deleteService'])->name('admin.services.delete');

    Route::get('/clients', [App\Http\Controllers\AdminController::class, 'getClients'])->name('admin.clients');
    Route::post('/clients', [App\Http\Controllers\AdminController::class, 'storeClient'])->name('admin.clients.store');
    Route::post('/clients/import', [App\Http\Controllers\AdminController::class, 'importClients'])->name('admin.clients.import');
    Route::post('/clients/{client}', [App\Http\Controllers\AdminController::class, 'updateClient'])->name('admin.clients.update');
    Route::delete('/clients/{client}', [App\Http\Controllers\AdminController::class, 'deleteClient'])->name('admin.clients.delete');

    Route::get('/services', [App\Http\Controllers\AdminController::class, 'services'])->name('admin.services');

    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});
