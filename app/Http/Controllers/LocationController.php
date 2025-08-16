<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Province;
use App\Models\Municipality;
use App\Models\Barangay;

class LocationController extends Controller
{
    public function provinces()
    {
        $provinces = Province::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($provinces);
    }

    public function citiesMunicipalities(Request $request)
    {
        $provinceId = $request->query('province_id') ?? $request->query('province_code');
        if (!$provinceId) {
            return response()->json([], 200);
        }

        $items = Municipality::query()
            ->where('province_id', $provinceId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($items);
    }

    public function barangays(Request $request)
    {
        $municipalityId = $request->query('municipality_id') ?? $request->query('city_municipality_code');
        if (!$municipalityId) {
            return response()->json([], 200);
        }

        $barangays = Barangay::query()
            ->where('municipality_id', $municipalityId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($barangays);
    }

    /**
     * Serve the preloaded locations JSON file
     * This file contains all provinces, municipalities, and barangays
     * for fast client-side cascading dropdowns
     */
    public function locationsJson()
    {
        $filePath = public_path('locations.json');
        
        if (!file_exists($filePath)) {
            return response()->json([
                'error' => 'Locations file not found. Please run: php artisan locations:generate-json'
            ], 404);
        }

        $lastModified = filemtime($filePath);
        $etag = md5_file($filePath);

        // Check if client has cached version
        $clientEtag = request()->header('If-None-Match');
        $clientLastModified = request()->header('If-Modified-Since');

        if ($clientEtag === $etag || 
            ($clientLastModified && strtotime($clientLastModified) >= $lastModified)) {
            return response('', 304);
        }

        return response()
            ->file($filePath)
            ->header('Content-Type', 'application/json')
            ->header('Cache-Control', 'public, max-age=86400') // Cache for 24 hours
            ->header('ETag', $etag)
            ->header('Last-Modified', gmdate('D, d M Y H:i:s T', $lastModified));
    }
}


