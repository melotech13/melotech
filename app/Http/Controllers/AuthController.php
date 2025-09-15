<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Farm;
use App\Services\EmailVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showRegistration()
    {
        // Preload provinces server-side to avoid client-side CORS/network issues
        $provinces = [];
        try {
            $response = Http::retry(2, 200)->timeout(15)->acceptJson()->withoutVerifying()->get('https://psgc.gitlab.io/api/provinces/');
            if ($response->failed()) {
                $response = Http::retry(1, 200)->timeout(15)->acceptJson()->withoutVerifying()->get('https://psgc.gitlab.io/api/provinces.json');
            }
            if ($response->failed()) {
                $response = Http::retry(1, 200)->timeout(15)->acceptJson()->withoutVerifying()->get('https://raw.githubusercontent.com/psgc-dev/psgc-data/main/provinces.json');
                if ($response->failed()) {
                    $response = Http::retry(1, 200)->timeout(15)->acceptJson()->withoutVerifying()->get('https://raw.githubusercontent.com/psgc-dev/psgc-data/master/provinces.json');
                }
            }
            $json = $response->json();
            if (is_array($json)) {
                $provinces = collect($json)
                    ->map(fn ($p) => [
                        'code' => $p['code'] ?? ($p['psgcCode'] ?? null),
                        'name' => $p['name'] ?? ($p['fullName'] ?? null),
                    ])
                    ->filter(fn ($p) => $p['code'] && $p['name'])
                    ->sortBy('name')
                    ->values()
                    ->all();
            }
        } catch (\Throwable $e) {
            $provinces = [];
        }

        return view('auth.register', [
            'provinces' => $provinces,
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => ['required', 'string', 'max:20'],
            
            // Location details
            'province_name' => ['required', 'string', 'max:255'],
            'city_municipality_name' => ['required', 'string', 'max:255'],
            'barangay_name' => ['nullable', 'string', 'max:255'],
            
            // Farm details
            'farm_name' => ['required', 'string', 'max:255'],
            'watermelon_variety' => ['required', 'string', 'max:255'],
            'planting_date' => ['required', 'date'],
            'land_size' => ['required', 'numeric', 'min:0.1'],
            'land_size_unit' => ['required', 'in:m2,ha'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create user record
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, // Store as plain text
            'phone' => $request->phone,
            'role' => 'user', // Default role for new registrations
        ]);

        // Create farm record
        $farm = Farm::create([
            'user_id' => $user->id,
            'farm_name' => $request->farm_name,
            'watermelon_variety' => $request->watermelon_variety,
            'planting_date' => $request->planting_date,
            'land_size' => $request->land_size,
            'land_size_unit' => $request->land_size_unit,
            'province_name' => $request->province_name,
            'city_municipality_name' => $request->city_municipality_name,
            'barangay_name' => $request->barangay_name,
        ]);

        // Send verification email
        $emailVerificationService = app(EmailVerificationService::class);
        $result = $emailVerificationService->sendVerificationEmail($user);

        if ($result['success']) {
            $message = $result['message'];
            
            // If using cache fallback, show the verification code
            if ($result['method'] === 'cache' && isset($result['code'])) {
                $message .= " Your verification code is: " . $result['code'];
            }
            
            return redirect()->route('verification.show')
                ->with('success', $message)
                ->with('email', $user->email);
        } else {
            return redirect()->back()
                ->with('error', $result['message'])
                ->withInput();
        }
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Enhanced authentication with better error handling
        $user = User::where('email', $credentials['email'])->first();
        
        if ($user) {
            // Check if password matches (support both plain text and hashed passwords)
            $passwordMatches = false;
            
            // First try plain text comparison (for admin users)
            if ($user->password === $credentials['password']) {
                $passwordMatches = true;
            }
            // Then try hashed password verification (for regular users)
            elseif (Hash::check($credentials['password'], $user->password)) {
                $passwordMatches = true;
            }
            
            if ($passwordMatches) {
                // Update last login time
                $user->update(['last_login_at' => now()]);
                
                // Login the user
                Auth::login($user, $request->boolean('remember'));
                $request->session()->regenerate();
                
                // Redirect based on user role
                if ($user->isAdmin()) {
                    return redirect()->intended('admin/dashboard');
                } else {
                    return redirect()->intended('dashboard');
                }
            }
        }

        // Log failed login attempt
        Log::warning('Failed login attempt', [
            'email' => $credentials['email'],
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Check if it's an AJAX request (automatic logout)
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Logged out successfully']);
        }
        
        return redirect()->route('home');
    }

    /**
     * Show the email verification form.
     */
    public function showVerification()
    {
        return view('auth.verify-email');
    }

    /**
     * Verify the user's email with the provided code.
     */
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'verification_code' => ['required', 'string', 'size:6'],
        ]);

        Log::info('AuthController::verifyEmail called', [
            'email' => $request->email,
            'verification_code' => $request->verification_code
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            Log::warning('User not found for verification', ['email' => $request->email]);
            return redirect()->back()
                ->withErrors(['email' => 'User not found.'])
                ->withInput();
        }

        Log::info('User found for verification', [
            'user_id' => $user->id,
            'email' => $user->email,
            'is_email_verified' => $user->isEmailVerified(),
            'email_verified_at' => $user->email_verified_at
        ]);

        if ($user->isEmailVerified()) {
            Log::info('User email already verified', ['user_id' => $user->id]);
            return redirect()->route('login')
                ->with('success', 'Your email is already verified. You can now log in.');
        }

        $emailVerificationService = app(EmailVerificationService::class);
        $result = $emailVerificationService->verifyEmail($user, $request->verification_code);
        
        Log::info('Verification result', [
            'user_id' => $user->id,
            'success' => $result['success'],
            'message' => $result['message']
        ]);
        
        if ($result['success']) {
            // Refresh user to get updated verification status
            $user->refresh();
            Log::info('User refreshed after verification', [
                'user_id' => $user->id,
                'is_email_verified' => $user->isEmailVerified(),
                'email_verified_at' => $user->email_verified_at
            ]);
            
            return redirect()->route('login')
                ->with('success', $result['message']);
        }

        return redirect()->back()
            ->withErrors(['verification_code' => $result['message']])
            ->withInput();
    }

    /**
     * Resend verification email.
     */
    public function resendVerification(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.'
                ]);
            }
            return redirect()->back()
                ->withErrors(['email' => 'User not found.'])
                ->withInput();
        }

        if ($user->isEmailVerified()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Your email is already verified. You can now log in.'
                ]);
            }
            return redirect()->route('login')
                ->with('success', 'Your email is already verified. You can now log in.');
        }

        $emailVerificationService = app(EmailVerificationService::class);
        $result = $emailVerificationService->resendVerificationEmail($user);
        
        if ($request->expectsJson()) {
            return response()->json($result);
        }
        
        if ($result['success']) {
            $message = $result['message'];
            
            // If using cache fallback, show the verification code
            if ($result['method'] === 'cache' && isset($result['code'])) {
                $message .= " Your verification code is: " . $result['code'];
            }
            
            return redirect()->back()
                ->with('success', $message);
        }

        return redirect()->back()
            ->with('error', $result['message']);
    }
}
