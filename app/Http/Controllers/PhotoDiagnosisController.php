<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\PhotoAnalysis;
use App\Services\Diagnosis\PhotoDiagnosisService;

class PhotoDiagnosisController extends Controller
{
	protected PhotoDiagnosisService $diagnosisService;

	public function __construct(PhotoDiagnosisService $diagnosisService)
	{
		$this->diagnosisService = $diagnosisService;
	}
    /**
     * Show the photo diagnosis page with analysis history.
     */
    public function index()
    {
        $user = Auth::user();
        $analyses = PhotoAnalysis::where('user_id', $user->id)
            ->orderBy('analysis_date', 'desc')
            ->paginate(10);

        return view('user.photo-diagnosis.index', compact('analyses'));
    }

    /**
     * Show the photo upload form.
     */
    public function create()
    {
        return view('user.photo-diagnosis.create');
    }

    /**
     * Store a new photo analysis.
     */
    public function store(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
            'analysis_type' => 'required|in:leaves,watermelon'
        ]);

        $user = Auth::user();

        $photoPath = $request->file('photo')->store('photo-analyses', 'public');
        Log::info('Photo stored at: ' . $photoPath);

        $result = $this->diagnosisService->analyze($request->file('photo'), $request->analysis_type);

        if (empty($result['identified_type']) || !isset($result['confidence_score'])) {
            Log::error('Diagnosis result incomplete', $result ?? []);
            return back()->withErrors(['photo' => 'Analysis failed. Please try again with a different photo.']);
        }

        $photoAnalysis = PhotoAnalysis::create([
            'user_id' => $user->id,
            'photo_path' => $photoPath,
            'analysis_type' => $request->analysis_type,
            'identified_type' => $result['identified_type'],
            'confidence_score' => $result['confidence_score'],
            'recommendations' => $result['recommendations'] ?? null,
            'analysis_date' => now(),
            'processing_time' => $result['processing_time'] ?? null,
            'image_metadata' => $result['image_metadata'] ?? null,
            'analysis_details' => $result['analysis_details'] ?? null,
        ]);

        return redirect()->route('photo-diagnosis.show', $photoAnalysis)
            ->with('success', 'Photo analysis completed successfully!');
    }

    /**
     * Show a specific photo analysis.
     */
    public function show(PhotoAnalysis $photoAnalysis)
    {
        // Ensure user can only view their own analyses
        if ($photoAnalysis->user_id !== Auth::id()) {
            abort(403);
        }

        return view('user.photo-diagnosis.show', compact('photoAnalysis'));
    }

}
