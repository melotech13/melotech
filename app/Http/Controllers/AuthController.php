<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Farm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Http;

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
            'field_size' => ['required', 'numeric', 'min:0.1'],
            'field_size_unit' => ['required', 'in:acres,hectares'],
            
            // Terms agreement
            'terms' => ['required', 'accepted'],
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
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'terms_accepted' => true,
        ]);

        // Create farm record
        $farm = Farm::create([
            'user_id' => $user->id,
            'farm_name' => $request->farm_name,
            'watermelon_variety' => $request->watermelon_variety,
            'planting_date' => $request->planting_date,
            'field_size' => $request->field_size,
            'field_size_unit' => $request->field_size_unit,
            'province_name' => $request->province_name,
            'city_municipality_name' => $request->city_municipality_name,
            'barangay_name' => $request->barangay_name,
        ]);

        // Redirect to login page with success message
        return redirect()->route('login')->with('success', 'Account created successfully! Please login with your email and password.');
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

        if (auth()->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
