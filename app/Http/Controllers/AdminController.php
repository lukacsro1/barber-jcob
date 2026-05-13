<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Http\Request;

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

        $appointments = $query->get(['id', 'user_id', 'customer_name', 'service', 'start_at', 'status']);
            
        $barbers = User::where('role', User::ROLE_BARBER)->get(['id', 'name']);

        return view('admin.appointments', [
            'appointments' => $appointments,
            'barbers' => $barbers,
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

        if ($request->hasFile('avatar')) {
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
}
