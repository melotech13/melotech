<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Farm;
use App\Models\PhotoAnalysis;
use App\Models\CropProgressUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_farms' => Farm::count(),
            'total_photo_analyses' => PhotoAnalysis::count(),
            'total_progress_updates' => CropProgressUpdate::count(),
            'recent_users' => User::where('role', 'user')->latest()->take(5)->get(),
            'recent_farms' => Farm::with('user')->latest()->take(5)->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Show all users.
     */
    public function users(Request $request)
    {
        $perPage = (int) $request->get('per_page', 15);
        $perPage = max(5, min($perPage, 100));

        // Single, unified paginated list including both admins and standard users
        $users = User::with('farms')
            ->orderByRaw("CASE WHEN role = 'admin' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();

        // Compute stats
        $totalUsers = User::count();
        $adminCount = User::where('role', 'admin')->count();
        $userCount = User::where('role', 'user')->count();
        $recentRegistrations = User::where('created_at', '>=', now()->subDays(30))->count();

        $stats = [
            'total_users' => $totalUsers,
            'admin_count' => $adminCount,
            'user_count' => $userCount,
            'recent_registrations' => $recentRegistrations,
        ];

        return view('admin.users.index', compact('users', 'stats', 'perPage'));
    }

    /**
     * Show user details.
     */
    public function showUser(User $user)
    {
        $user->load(['farms', 'photoAnalyses', 'cropProgressUpdates']);

        if (request()->ajax() || request()->boolean('modal')) {
            return view('admin.users._modal_show', compact('user'));
        }

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form to edit a user.
     */
    public function editUser(User $user)
    {
        if (request()->ajax() || request()->boolean('modal')) {
            return view('admin.users._modal_edit', compact('user'));
        }
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update a user.
     */
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'role' => ['required', Rule::in(['user', 'admin'])],
        ]);

        $user->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully.',
                'user' => $user->fresh(),
            ]);
        }

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    /**
     * Show the form to create a new user.
     */
    public function createUser()
    {
        // Default varieties mirroring the registration page (values used as submitted values)
        $defaultVarieties = [
            'Cantaloupe / Muskmelon',
            'Honeydew Melon',
            'Watermelon',
            'Winter Melon',
            'Bitter Melon',
            'Snake Melon',
        ];

        // Pull distinct varieties from existing farms as DB-backed extras
        $dbVarieties = Farm::query()
            ->whereNotNull('watermelon_variety')
            ->where('watermelon_variety', '!=', '')
            ->distinct()
            ->orderBy('watermelon_variety')
            ->pluck('watermelon_variety')
            ->toArray();

        $extraVarieties = array_values(array_diff($dbVarieties, $defaultVarieties));

        return view('admin.users.create', compact('defaultVarieties', 'extraVarieties'));
    }

    /**
     * Store a new user.
     */
    public function storeUser(Request $request)
    {
        // Base validation for account fields
        $baseRules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'role' => ['required', Rule::in(['user', 'admin'])],
        ];

        // Determine role early to set farm rules conditionally
        $role = $request->input('role', 'user');

        $farmRulesWhenUser = [
            'farm_name' => 'required|string|max:255',
            'province_name' => 'required|string|max:255',
            'city_municipality_name' => 'required|string|max:255',
            'barangay_name' => 'nullable|string|max:255',
            'watermelon_variety' => 'nullable|string|max:255',
            'planting_date' => 'nullable|date',
            'field_size' => 'nullable|numeric|min:0',
            'field_size_unit' => 'nullable|string|max:20',
        ];

        $farmRulesWhenAdmin = [
            'farm_name' => 'nullable|string|max:255',
            'province_name' => 'nullable|string|max:255',
            'city_municipality_name' => 'nullable|string|max:255',
            'barangay_name' => 'nullable|string|max:255',
            'watermelon_variety' => 'nullable|string|max:255',
            'planting_date' => 'nullable|date',
            'field_size' => 'nullable|numeric|min:0',
            'field_size_unit' => 'nullable|string|max:20',
        ];

        $rules = array_merge($baseRules, $role === 'admin' ? $farmRulesWhenAdmin : $farmRulesWhenUser);
        $validated = $request->validate($rules);

        $userData = collect($validated)->only(['name','email','password','phone','role'])->toArray();
        $userData['password'] = Hash::make($userData['password']);

        $user = User::create($userData);

        // Removed email verified flag handling as per requirements

        // Create farm only for non-admin users
        if (strtolower($user->role) === 'user') {
            Farm::create([
                'user_id' => $user->id,
                'farm_name' => $validated['farm_name'] ?? '',
                'province_name' => $validated['province_name'] ?? '',
                'city_municipality_name' => $validated['city_municipality_name'] ?? '',
                'barangay_name' => $validated['barangay_name'] ?? null,
                'watermelon_variety' => $validated['watermelon_variety'] ?? '',
                'planting_date' => $validated['planting_date'] ?? null,
                'field_size' => $validated['field_size'] ?? null,
                'field_size_unit' => $validated['field_size_unit'] ?? null,
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Delete a user.
     */
    public function deleteUser(User $user)
    {
        // Prevent admin from deleting themselves
        if ($user->id === Auth::id()) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot delete your own account.'
                ], 422);
            }
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.'
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Show all farms.
     */
    public function farms()
    {
        $farms = Farm::with('user')->paginate(15);

        // Stats to mirror Users page style
        $stats = [
            'total_farms' => Farm::count(),
            'unique_owners' => Farm::distinct('user_id')->count('user_id'),
            'active_farms' => Farm::whereNotNull('planting_date')->count(),
            'recent_additions' => Farm::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        return view('admin.farms.index', compact('farms', 'stats'));
    }

    /**
     * Show farm details.
     */
    public function showFarm(Farm $farm)
    {
        $farm->load(['user', 'cropGrowth', 'cropProgressUpdates']);
        if (request()->ajax() || request()->boolean('modal')) {
            return view('admin.farms._modal_show', compact('farm'));
        }
        return view('admin.farms.show', compact('farm'));
    }

    /**
     * Show system statistics.
     */
    public function statistics()
    {
        $stats = [
            'users' => [
                'total' => User::count(),
                'admins' => User::where('role', 'admin')->count(),
                'regular_users' => User::where('role', 'user')->count(),
                'new_this_month' => User::whereMonth('created_at', now()->month)->count(),
            ],
            'farms' => [
                'total' => Farm::count(),
                'active' => Farm::whereHas('cropGrowth')->count(),
                'new_this_month' => Farm::whereMonth('created_at', now()->month)->count(),
            ],
            'analyses' => [
                'total' => PhotoAnalysis::count(),
                'this_month' => PhotoAnalysis::whereMonth('created_at', now()->month)->count(),
            ],
            'updates' => [
                'total' => CropProgressUpdate::count(),
                'this_month' => CropProgressUpdate::whereMonth('created_at', now()->month)->count(),
            ],
        ];

        return view('admin.statistics', compact('stats'));
    }

    /**
     * Show the form to edit a farm.
     */
    public function editFarm(Farm $farm)
    {
        if (request()->ajax() || request()->boolean('modal')) {
            return view('admin.farms._modal_edit', compact('farm'));
        }
        return view('admin.farms.edit', compact('farm'));
    }

    /**
     * Update a farm.
     */
    public function updateFarm(Request $request, Farm $farm)
    {
        $validated = $request->validate([
            'farm_name' => 'required|string|max:255',
            'watermelon_variety' => 'nullable|string|max:255',
            'province_name' => 'required|string|max:255',
            'city_municipality_name' => 'required|string|max:255',
            'barangay_name' => 'nullable|string|max:255',
            'field_size' => 'nullable|numeric|min:0',
            'field_size_unit' => 'nullable|string|max:20',
            'planting_date' => 'nullable|date',
        ]);

        $farm->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Farm updated successfully.',
                'farm' => $farm->fresh('user'),
            ]);
        }

        return redirect()->route('admin.farms.show', $farm)
            ->with('success', 'Farm updated successfully.');
    }

    /**
     * Delete a farm.
     */
    public function deleteFarm(Farm $farm)
    {
        $user = $farm->user; // owner
        $userDeleted = false;
        $shouldDeleteUser = false;

        if ($user) {
            // If this is the owner's only farm, mark the owner for deletion (avoid deleting admins)
            $shouldDeleteUser = ($user->farms()->count() === 1) && (strtolower($user->role) !== 'admin');
        }

        // Delete the farm first
        $farm->delete();

        // Optionally delete the owning user account
        if ($shouldDeleteUser) {
            $user->delete();
            $userDeleted = true;
        }

        $message = $userDeleted
            ? 'Farm and its owner account were deleted successfully.'
            : 'Farm deleted successfully.';

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'user_deleted' => $userDeleted,
                'message' => $message,
            ]);
        }

        return redirect()->route('admin.farms.index')->with('success', $message);
    }

    /**
     * Show admin settings page.
     */
    public function settings()
    {
        return view('admin.settings');
    }

}

