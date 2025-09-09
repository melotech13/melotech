<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Farm;
use App\Models\PhotoAnalysis;
use App\Models\CropProgressUpdate;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function dashboard()
    {
        // Basic stats
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_farms' => Farm::count(),
            'total_photo_analyses' => PhotoAnalysis::count(),
            'total_progress_updates' => CropProgressUpdate::count(),
        ];

        // Recent Activity Feed data
        $activityFeed = $this->getActivityFeed();

        // Daily activity data for the last 7 days
        $dailyActivity = $this->getDailyActivityData();

        return view('admin.dashboard', compact('stats', 'activityFeed', 'dailyActivity'));
    }

    /**
     * Get daily activity data for the last 7 days.
     */
    private function getDailyActivityData()
    {
        try {
            $dailyActivity = [];
            
            // Get data for the last 7 days
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $startOfDay = $date->copy()->startOfDay();
                $endOfDay = $date->copy()->endOfDay();
                
                // Count new users registered on this day
                $newUsers = User::where('role', 'user')
                    ->whereBetween('created_at', [$startOfDay, $endOfDay])
                    ->count();
                
                // Count new farms created on this day
                $newFarms = Farm::whereBetween('created_at', [$startOfDay, $endOfDay])
                    ->count();
                
                // Count photo analyses created on this day
                $newAnalyses = PhotoAnalysis::whereBetween('created_at', [$startOfDay, $endOfDay])
                    ->count();
                
                $dailyActivity[] = [
                    'date' => $date->format('M d'),
                    'date_full' => $date->format('Y-m-d'),
                    'new_users' => $newUsers,
                    'new_farms' => $newFarms,
                    'new_analyses' => $newAnalyses,
                    'total_activity' => $newUsers + $newFarms + $newAnalyses,
                ];
            }
            
            return $dailyActivity;
        } catch (\Exception $e) {
            Log::error('Error getting daily activity data: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get recent activity feed data.
     */
    private function getActivityFeed()
    {
        try {
            $activities = [];
            
            // Get recent users
            $recentUsers = User::where('role', 'user')
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get();
            
            foreach ($recentUsers as $user) {
                $activities[] = [
                    'type' => 'user_registered',
                    'icon' => 'fas fa-user-plus',
                    'title' => 'New User Registered',
                    'description' => $user->name . ' joined the platform',
                    'time' => $user->created_at->diffForHumans(),
                    'color' => 'blue'
                ];
            }
            
            // Get recent farms
            $recentFarms = Farm::with('user')
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get();
            
            foreach ($recentFarms as $farm) {
                $activities[] = [
                    'type' => 'farm_created',
                    'icon' => 'fas fa-seedling',
                    'title' => 'New Farm Created',
                    'description' => $farm->user->name . ' created "' . $farm->farm_name . '"',
                    'time' => $farm->created_at->diffForHumans(),
                    'color' => 'green'
                ];
            }
            
            // Get recent photo analyses
            $recentAnalyses = PhotoAnalysis::with('user')
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get();
            
            foreach ($recentAnalyses as $analysis) {
                $activities[] = [
                    'type' => 'photo_analyzed',
                    'icon' => 'fas fa-camera',
                    'title' => 'Photo Analysis Completed',
                    'description' => $analysis->user->name . ' analyzed a crop photo',
                    'time' => $analysis->created_at->diffForHumans(),
                    'color' => 'purple'
                ];
            }
            
            // Get recent progress updates
            $recentUpdates = CropProgressUpdate::with(['user', 'farm'])
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get();
            
            foreach ($recentUpdates as $update) {
                $activities[] = [
                    'type' => 'progress_updated',
                    'icon' => 'fas fa-chart-line',
                    'title' => 'Progress Update',
                    'description' => $update->user->name . ' updated progress for ' . $update->farm->farm_name,
                    'time' => $update->created_at->diffForHumans(),
                    'color' => 'orange'
                ];
            }
            
            // Sort all activities by time (most recent first)
            usort($activities, function($a, $b) {
                return strtotime($b['time']) - strtotime($a['time']);
            });
            
            // Take only the 4 most recent activities to match Quick Actions height
            $activities = array_slice($activities, 0, 4);
            
            return [
                'activities' => $activities,
                'total_activities' => count($activities),
                'last_updated' => now()->format('M j, Y \a\t g:i A')
            ];
        } catch (\Exception $e) {
            Log::error('Error getting activity feed: ' . $e->getMessage());
            return [
                'activities' => [],
                'total_activities' => 0,
                'last_updated' => 'Unable to load'
            ];
        }
    }


    /**
     * Count files in a directory recursively.
     */
    private function countFilesInDirectory($directory)
    {
        $count = 0;
        if (is_dir($directory)) {
            $files = glob($directory . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    $count++;
                } elseif (is_dir($file)) {
                    $count += $this->countFilesInDirectory($file);
                }
            }
        }
        return $count;
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
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
                'phone' => 'nullable|string|max:20',
                'password' => 'required|string|min:8',
                'role' => ['required', Rule::in(['user', 'admin'])],
            ]);

            // Store password as provided (system supports plain or hashed)
            $user->update($validated);

            // Notify about admin-updated user
            NotificationService::notifyAdminUpdatedUser(Auth::user(), $user);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User updated successfully.',
                    'user' => $user->fresh(),
                ]);
            }

            return redirect()->route('admin.users.show', $user);
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'request_data' => $request->all()
            ]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the user.',
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'An error occurred while updating the user.')
                ->withInput();
        }
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
        // Store password as plain text (no hashing)

        $user = User::create($userData);

        // Notify about admin-created user
        NotificationService::notifyAdminCreatedUser(Auth::user(), $user);

        // Removed email verified flag handling as per requirements

        // Create farm only for non-admin users
        if (strtolower($user->role) === 'user') {
            $farm = Farm::create([
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
            
            // Notify about admin-created farm
            NotificationService::notifyAdminCreatedFarm(Auth::user(), $farm);
        }

        return redirect()->route('admin.users.index');
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

        // Store user info before deletion for notification
        $userName = $user->name;
        $userEmail = $user->email;
        
        $user->delete();

        // Notify about admin-deleted user
        NotificationService::notifyAdminDeletedUser(Auth::user(), $userName, $userEmail);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.'
            ]);
        }

        return redirect()->route('admin.users.index');
    }

    /**
     * Show all farms.
     */
    public function farms(Request $request)
    {
        $perPage = (int) $request->get('per_page', 15);
        $perPage = max(5, min($perPage, 100));
        $search = $request->get('q', '');
        $filter = $request->get('filter', 'all');

        // Build query with search functionality
        $query = Farm::with('user');

        // Apply search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('farm_name', 'like', '%' . $search . '%')
                  ->orWhere('watermelon_variety', 'like', '%' . $search . '%')
                  ->orWhere('province_name', 'like', '%' . $search . '%')
                  ->orWhere('city_municipality_name', 'like', '%' . $search . '%')
                  ->orWhere('barangay_name', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', '%' . $search . '%')
                               ->orWhere('email', 'like', '%' . $search . '%');
                  });
            });
        }

        // Apply status filter
        switch ($filter) {
            case 'active':
                $query->whereNotNull('planting_date');
                break;
            case 'inactive':
                $query->whereNull('planting_date');
                break;
            case 'recent':
                $query->where('created_at', '>=', now()->subDays(30));
                break;
            case 'all':
            default:
                // No additional filter
                break;
        }

        $farms = $query->orderBy('farm_name')->paginate($perPage)->withQueryString();

        // Stats to mirror Users page style
        $stats = [
            'total_farms' => Farm::count(),
            'unique_owners' => Farm::distinct('user_id')->count('user_id'),
            'active_farms' => Farm::whereNotNull('planting_date')->count(),
            'recent_additions' => Farm::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        return view('admin.farms.index', compact('farms', 'stats', 'perPage', 'search', 'filter'));
    }

    /**
     * Show farm details.
     */
    public function showFarm(Farm $farm)
    {
        $farm->load(['user', 'cropGrowth', 'cropProgressUpdates']);
        return view('admin.farms._modal_show', compact('farm'));
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
            return view('admin.farms.modal_edit', compact('farm'));
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

        // Notify about admin-updated farm
        NotificationService::notifyAdminUpdatedFarm(Auth::user(), $farm);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Farm updated successfully.',
                'farm' => $farm->fresh('user'),
            ]);
        }

        return redirect()->route('admin.farms.show', $farm);
    }

    /**
     * Delete a farm.
     */
    public function deleteFarm(Farm $farm)
    {
        $user = $farm->user; // owner
        $userDeleted = false;
        $shouldDeleteUser = false;

        // Store farm info before deletion for notification
        $farmName = $farm->farm_name;
        $ownerName = $user ? $user->name : 'Unknown';

        if ($user) {
            // If this is the owner's only farm, mark the owner for deletion (avoid deleting admins)
            $shouldDeleteUser = ($user->farms()->count() === 1) && (strtolower($user->role) !== 'admin');
        }

        // Delete the farm first
        $farm->delete();

        // Notify about admin-deleted farm
        NotificationService::notifyAdminDeletedFarm(Auth::user(), $farmName, $ownerName);

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

        return redirect()->route('admin.farms.index');
    }

    /**
     * Show admin settings page.
     */
    public function settings()
    {
        return view('admin.settings');
    }

    /**
     * Show notifications page.
     */
    public function notifications()
    {
        $currentUser = Auth::user();
        
        // Get notifications for the current user only
        $notifications = Notification::where('user_id', $currentUser->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.notifications', compact('notifications'));
    }

    /**
     * Update admin profile settings.
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
        ]);

        User::where('id', $user->id)->update($validated);

        return redirect()->route('admin.settings')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Update admin password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Check current password
        if ($validated['current_password'] !== $user->password) {
            return redirect()->route('admin.settings')
                ->with('error', 'Current password is incorrect.');
        }

        User::where('id', $user->id)->update([
            'password' => $validated['password'] // Store as plain text
        ]);

        return redirect()->route('admin.settings')
            ->with('success', 'Password updated successfully.');
    }

    /**
     * Export users as PDF
     */
    public function exportUsersPDF()
    {
        $users = User::with('farms')
            ->orderByRaw("CASE WHEN role = 'admin' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get();

        $data = [
            'users' => $users,
            'exportDate' => Carbon::now()->format('M d, Y H:i:s'),
            'totalUsers' => $users->count(),
            'adminCount' => $users->where('role', 'admin')->count(),
            'userCount' => $users->where('role', 'user')->count(),
        ];

        // Generate PDF using DomPDF
        $pdf = Pdf::loadView('admin.users.pdf-report', $data);
        
        $fileName = 'users_management_' . Carbon::now()->format('Y-m-d') . '.pdf';
        
        return $pdf->download($fileName);
    }

    /**
     * Print users report
     */
    public function printUsers()
    {
        $users = User::with('farms')
            ->orderByRaw("CASE WHEN role = 'admin' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get();

        $data = [
            'users' => $users,
            'exportDate' => Carbon::now()->format('M d, Y H:i:s'),
            'totalUsers' => $users->count(),
            'adminCount' => $users->where('role', 'admin')->count(),
            'userCount' => $users->where('role', 'user')->count(),
        ];

        // Add cache control headers to prevent caching issues
        return response()->view('admin.users.pdf-report', $data)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Export farms as PDF
     */
    public function exportFarmsPDF(Request $request)
    {
        $search = $request->get('q', '');
        $filter = $request->get('filter', 'all');
        
        // Build query with search functionality (same as farms method)
        $query = Farm::with('user');

        // Apply search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('farm_name', 'like', '%' . $search . '%')
                  ->orWhere('watermelon_variety', 'like', '%' . $search . '%')
                  ->orWhere('province_name', 'like', '%' . $search . '%')
                  ->orWhere('city_municipality_name', 'like', '%' . $search . '%')
                  ->orWhere('barangay_name', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', '%' . $search . '%')
                               ->orWhere('email', 'like', '%' . $search . '%');
                  });
            });
        }

        // Apply status filter
        switch ($filter) {
            case 'active':
                $query->whereNotNull('planting_date');
                break;
            case 'inactive':
                $query->whereNull('planting_date');
                break;
            case 'recent':
                $query->where('created_at', '>=', now()->subDays(30));
                break;
            case 'all':
            default:
                // No additional filter
                break;
        }

        $farms = $query->orderBy('farm_name')->get();

        $data = [
            'farms' => $farms,
            'exportDate' => Carbon::now()->format('M d, Y H:i:s'),
            'totalFarms' => $farms->count(),
            'uniqueOwners' => $farms->pluck('user_id')->unique()->count(),
            'activeFarms' => $farms->whereNotNull('planting_date')->count(),
            'searchTerm' => $search,
        ];

        // Generate PDF using DomPDF
        $pdf = Pdf::loadView('admin.farms.pdf-report', $data);
        
        $fileName = 'farms_management_' . Carbon::now()->format('Y-m-d') . '.pdf';
        if (!empty($search)) {
            $fileName = 'farms_management_' . str_replace(' ', '_', $search) . '_' . Carbon::now()->format('Y-m-d') . '.pdf';
        }
        
        return $pdf->download($fileName);
    }

    /**
     * Print farms report
     */
    public function printFarms(Request $request)
    {
        $search = $request->get('q', '');
        $filter = $request->get('filter', 'all');
        
        // Build query with search functionality (same as farms method)
        $query = Farm::with('user');

        // Apply search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('farm_name', 'like', '%' . $search . '%')
                  ->orWhere('watermelon_variety', 'like', '%' . $search . '%')
                  ->orWhere('province_name', 'like', '%' . $search . '%')
                  ->orWhere('city_municipality_name', 'like', '%' . $search . '%')
                  ->orWhere('barangay_name', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', '%' . $search . '%')
                               ->orWhere('email', 'like', '%' . $search . '%');
                  });
            });
        }

        // Apply status filter
        switch ($filter) {
            case 'active':
                $query->whereNotNull('planting_date');
                break;
            case 'inactive':
                $query->whereNull('planting_date');
                break;
            case 'recent':
                $query->where('created_at', '>=', now()->subDays(30));
                break;
            case 'all':
            default:
                // No additional filter
                break;
        }

        $farms = $query->orderBy('farm_name')->get();

        $data = [
            'farms' => $farms,
            'exportDate' => Carbon::now()->format('M d, Y H:i:s'),
            'totalFarms' => $farms->count(),
            'uniqueOwners' => $farms->pluck('user_id')->unique()->count(),
            'activeFarms' => $farms->whereNotNull('planting_date')->count(),
            'searchTerm' => $search,
        ];

        // Add cache control headers to prevent caching issues
        return response()->view('admin.farms.pdf-report', $data)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Get notifications for dropdown display.
     */
    public function getDropdownNotifications()
    {
        $currentUser = Auth::user();
        
        // Get recent notifications for the current user only
        $notifications = Notification::where('user_id', $currentUser->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get unread count for the current user only
        $unreadCount = Notification::where('user_id', $currentUser->id)
            ->where('read', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllNotificationsRead()
    {
        $currentUser = Auth::user();
        
        // Mark all notifications as read for the current user only
        Notification::where('user_id', $currentUser->id)
            ->where('read', false)
            ->update(['read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * Get unread notifications count.
     */
    public function getUnreadCount()
    {
        $currentUser = Auth::user();
        
        // Get unread count for the current user only
        $unreadCount = Notification::where('user_id', $currentUser->id)
            ->where('read', false)
            ->count();

        return response()->json([
            'unread_count' => $unreadCount
        ]);
    }


}

