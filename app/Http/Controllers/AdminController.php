<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentNotification;

class AdminController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();

        $stats = [
            'revenue' => 420.00,
            'active_barbers' => User::where('role', User::ROLE_BARBER)->count(),
            'today_appointments' => $user->isAdmin()
                ? Appointment::whereDate('start_at', today())->count()
                : Appointment::where('user_id', $user->id)->whereDate('start_at', today())->count(),
            'pending_appointments' => $user->isAdmin()
                ? Appointment::where('status', 'scheduled')->count()
                : Appointment::where('user_id', $user->id)->where('status', 'scheduled')->count(),
        ];

        $services = Service::all(['id', 'name', 'price', 'duration_minutes']);

        return view('admin.dashboard', [
            'stats' => $stats,
            'services' => $services,
            'user' => [
                'name' => auth()->user()->name,
                'role' => auth()->user()->role,
                'show_in_gallery' => auth()->user()->show_in_gallery
            ],
            'pageTitle' => 'Dashboard'
        ]);
    }

    public function storeService(Request $request)
    {
        abort_if(!auth()->user()->isAdmin(), 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:5',
        ]);

        $service = Service::create($data);

        return response()->json($service);
    }

    public function deleteService(Service $service)
    {
        abort_if(!auth()->user()->isAdmin(), 403);
        $service->delete();
        return response()->json(['success' => true]);
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'show_in_gallery' => 'required|boolean'
        ]);

        auth()->user()->update($data);

        return response()->json(['success' => true]);
    }

    public function appointments()
    {
        $query = Appointment::with('barber:id,name');

        if (auth()->user()->isBarber()) {
            $query->where('user_id', auth()->id());
        }

        $appointments = $query->get(['id', 'user_id', 'customer_name', 'customer_phone', 'service', 'start_at', 'status']);

        $barbers = User::where('role', User::ROLE_BARBER)->get(['id', 'name']);
        $services = Service::all(['id', 'name', 'price', 'duration_minutes']);

        return view('admin.appointments', [
            'appointments' => $appointments,
            'barbers' => $barbers,
            'services' => $services,
            'user' => [
                'name' => auth()->user()->name,
                'role' => auth()->user()->role
            ],
            'pageTitle' => 'Appointments'
        ]);
    }

    public function barbers()
    {
        abort_if(!auth()->user()->isAdmin(), 403);

        $barbers = User::where('role', User::ROLE_BARBER)
            ->get(['id', 'name', 'email', 'phone', 'specialty', 'avatar_url', 'show_in_gallery'])
            ->map(function ($user) {
                if ($user->avatar_url) {
                    $user->avatar_url = \Illuminate\Support\Facades\Storage::url($user->avatar_url);
                }
                return $user;
            });

        return view('admin.barbers', [
            'barbers' => $barbers,
            'user' => [
                'name' => auth()->user()->name,
                'role' => auth()->user()->role
            ],
            'pageTitle' => 'Barbers'
        ]);
    }

    public function storeBarber(Request $request)
    {
        abort_if(!auth()->user()->isAdmin(), 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'specialty' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'show_in_gallery' => 'nullable',
            'avatar' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar_url'] = $request->file('avatar')->store('avatars', 'public');
        }
        $data['show_in_gallery'] = filter_var($data['show_in_gallery'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $data['role'] = User::ROLE_BARBER;

        $barber = User::create($data);
        $barber->seedDefaultSchedule();

        if ($barber->avatar_url) {
            $barber->avatar_url = \Illuminate\Support\Facades\Storage::url($barber->avatar_url);
        }

        return response()->json($barber);
    }

    public function updateBarber(Request $request, User $barber)
    {
        abort_if(!auth()->user()->isAdmin(), 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $barber->id,
            'password' => 'nullable|string|min:8',
            'specialty' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'show_in_gallery' => 'nullable',
            'avatar' => 'nullable|image|max:2048',
        ]);

        if (filter_var($request->input('delete_avatar'), FILTER_VALIDATE_BOOLEAN)) {
            if ($barber->avatar_url && \Illuminate\Support\Facades\Storage::disk('public')->exists($barber->avatar_url)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($barber->avatar_url);
            }
            $data['avatar_url'] = null;
        } elseif ($request->hasFile('avatar')) {
            if ($barber->avatar_url && \Illuminate\Support\Facades\Storage::disk('public')->exists($barber->avatar_url)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($barber->avatar_url);
            }
            $data['avatar_url'] = $request->file('avatar')->store('avatars', 'public');
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $data['show_in_gallery'] = filter_var($data['show_in_gallery'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $barber->update($data);

        if ($barber->avatar_url) {
            $barber->avatar_url = \Illuminate\Support\Facades\Storage::url($barber->avatar_url);
        }

        return response()->json($barber);
    }

    public function getClients()
    {
        abort_if(!auth()->user()->isAdmin(), 403);

        $clients = User::where('role', User::ROLE_CUSTOMER)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone']);

        return view('admin.clients', [
            'clients' => $clients,
            'user' => [
                'name' => auth()->user()->name,
                'role' => auth()->user()->role
            ],
            'pageTitle' => 'Clients'
        ]);
    }

    public function storeClient(Request $request)
    {
        abort_if(!auth()->user()->isAdmin(), 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:255',
        ]);

        $data['role'] = User::ROLE_CUSTOMER;
        $data['password'] = bcrypt(\Illuminate\Support\Str::random(16)); // Random password for customers

        $client = User::create($data);

        return response()->json($client);
    }

    public function updateClient(Request $request, User $client)
    {
        abort_if(!auth()->user()->isAdmin(), 403);
        abort_if($client->role !== User::ROLE_CUSTOMER, 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $client->id,
            'phone' => 'nullable|string|max:255',
        ]);

        $client->update($data);

        return response()->json($client);
    }

    public function getSchedule(User $barber)
    {
        abort_if(!auth()->user()->isAdmin(), 403);

        $schedules = $barber->schedules()->orderBy('day_of_week')->get();
        $daysOff = $barber->daysOff()->orderBy('date')->get();

        return response()->json([
            'schedules' => $schedules,
            'days_off' => $daysOff
        ]);
    }

    public function updateSchedule(Request $request, User $barber)
    {
        abort_if(!auth()->user()->isAdmin(), 403);

        $request->validate([
            'schedules' => 'required|array|size:7',
            'schedules.*.day_of_week' => 'required|integer|between:0,6',
            'schedules.*.is_working' => 'required|boolean',
            'schedules.*.start_time' => 'nullable|string',
            'schedules.*.end_time' => 'nullable|string',
        ]);

        foreach ($request->input('schedules') as $sched) {
            $barber->schedules()->updateOrCreate(
                ['day_of_week' => $sched['day_of_week']],
                [
                    'is_working' => $sched['is_working'],
                    'start_time' => $sched['is_working'] ? $sched['start_time'] : null,
                    'end_time' => $sched['is_working'] ? $sched['end_time'] : null,
                ]
            );
        }

        return response()->json(['success' => true]);
    }

    public function addDayOff(Request $request, User $barber)
    {
        abort_if(!auth()->user()->isAdmin(), 403);

        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'reason' => 'nullable|string|max:255',
        ]);

        $dayOff = $barber->daysOff()->updateOrCreate(
            ['date' => $request->input('date')],
            ['reason' => $request->input('reason')]
        );

        return response()->json($dayOff);
    }

    public function deleteDayOff(User $barber, \App\Models\BarberDayOff $dayOff)
    {
        abort_if(!auth()->user()->isAdmin(), 403);
        abort_if($dayOff->user_id !== $barber->id, 403);

        $dayOff->delete();

        return response()->json(['success' => true]);
    }

    public function services()
    {
        abort_if(!auth()->user()->isAdmin(), 403);

        $services = \App\Models\Service::orderBy('name')->get();

        return view('admin.services', [
            'services' => $services,
            'user' => [
                'name' => auth()->user()->name,
                'role' => auth()->user()->role
            ],
            'pageTitle' => 'Services'
        ]);
    }

    public function storeAppointment(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:255',
            'service' => 'required|string|max:255',
            'start_at' => 'required|date',
            'status' => 'nullable|string|in:scheduled,completed,cancelled',
        ]);

        if (auth()->user()->isBarber()) {
            abort_if((int)$data['user_id'] !== auth()->id(), 403);
        }

        $data['status'] = $data['status'] ?? 'scheduled';

        $appointment = Appointment::create($data);
        $appointment->load('barber:id,name,email');

        try {
            Mail::to($appointment->barber->email)->send(new AppointmentNotification($appointment, 'booked'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send booking email from admin: ' . $e->getMessage());
        }

        return response()->json($appointment);
    }

    public function updateAppointment(Request $request, Appointment $appointment)
    {
        if (auth()->user()->isBarber()) {
            abort_if($appointment->user_id !== auth()->id(), 403);
        }

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:255',
            'service' => 'required|string|max:255',
            'start_at' => 'required|date',
            'status' => 'required|string|in:scheduled,completed,cancelled',
        ]);

        if (auth()->user()->isBarber()) {
            abort_if((int)$data['user_id'] !== auth()->id(), 403);
        }

        $appointment->update($data);
        $appointment->load('barber:id,name,email');

        try {
            Mail::to($appointment->barber->email)->send(new AppointmentNotification($appointment, 'updated'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send update email: ' . $e->getMessage());
        }

        return response()->json($appointment);
    }

    public function deleteAppointment(Appointment $appointment)
    {
        if (auth()->user()->isBarber()) {
            abort_if($appointment->user_id !== auth()->id(), 403);
        }

        $appointment->load('barber:id,name,email');

        try {
            Mail::to($appointment->barber->email)->send(new AppointmentNotification($appointment, 'cancelled'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send cancellation email: ' . $e->getMessage());
        }

        $appointment->delete();

        return response()->json(['success' => true]);
    }
}

