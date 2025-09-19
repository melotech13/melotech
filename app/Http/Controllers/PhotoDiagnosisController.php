<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Models\PhotoAnalysis;
use App\Services\Diagnosis\PhotoDiagnosisService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

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
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get all analyses for the user, ordered by most recent
        $analyses = PhotoAnalysis::where('user_id', $user->id)
            ->orderBy('analysis_date', 'desc')
            ->get();

        // Compute stats
        $totalAnalyses = PhotoAnalysis::where('user_id', $user->id)->count();
        $leavesCount = PhotoAnalysis::where('user_id', $user->id)->where('analysis_type', 'leaves')->count();
        $watermelonCount = PhotoAnalysis::where('user_id', $user->id)->where('analysis_type', 'watermelon')->count();
        $recentAnalyses = PhotoAnalysis::where('user_id', $user->id)->where('created_at', '>=', now()->subDays(30))->count();

        $stats = [
            'total_analyses' => $totalAnalyses,
            'leaves_count' => $leavesCount,
            'watermelon_count' => $watermelonCount,
            'recent_analyses' => $recentAnalyses,
        ];

        return view('user.photo-diagnosis.index', compact('analyses', 'stats'));
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

        // Analyze FIRST while the temporary file is intact
        $uploaded = $request->file('photo');
        $result = $this->diagnosisService->analyze($uploaded, $request->analysis_type);

        // Then save directly under public/uploads so no storage:link is required
        $uploadDir = public_path('uploads/photo-analyses');
        if (!File::exists($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true);
        }
        $originalName = pathinfo($uploaded->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $uploaded->getClientOriginalExtension();
        $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $originalName);
        $fileName = $safeName . '_' . time() . '.' . $extension;
        $uploaded->move($uploadDir, $fileName);
        $photoPath = 'uploads/photo-analyses/' . $fileName;
        Log::info('Photo stored at public path: ' . $photoPath);

        if (empty($result['identified_type']) || !isset($result['confidence_score'])) {
            Log::error('Diagnosis result incomplete', $result ?? []);
            return back()->withErrors(['photo' => 'Analysis failed. Please try again with a different photo.']);
        }

        $photoAnalysis = PhotoAnalysis::create([
            'user_id' => $user->id,
            'photo_path' => $photoPath,
            'analysis_type' => $request->analysis_type,
            'identified_type' => $result['identified_type'],
            'identified_condition' => $result['identified_condition'] ?? null,
            'condition_key' => $result['condition_key'] ?? null,
            'confidence_score' => $result['confidence_score'],
            'recommendations' => $result['recommendations'] ?? null,
            'analysis_date' => now(),
            'processing_time' => $result['processing_time'] ?? null,
            'image_metadata' => $result['image_metadata'] ?? null,
            'analysis_details' => $result['analysis_details'] ?? null,
            'condition_scores' => $result['condition_scores'] ?? null,
            'model_version' => $result['model_version'] ?? null,
        ]);

        return redirect()->route('photo-diagnosis.show', $photoAnalysis);
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

    /**
     * Delete a photo analysis.
     */
    public function destroy(PhotoAnalysis $photoAnalysis)
    {
        // Ensure user can only delete their own analyses
        if ($photoAnalysis->user_id !== Auth::id()) {
            abort(403);
        }

        // Delete the physical photo file if it exists
        if ($photoAnalysis->photo_path) {
            $photoPath = public_path($photoAnalysis->photo_path);
            if (File::exists($photoPath)) {
                File::delete($photoPath);
                Log::info('Deleted photo file: ' . $photoPath);
            }
        }

        // Delete the database record
        $photoAnalysis->delete();

        // Return JSON response for AJAX requests
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Analysis deleted successfully.'
            ]);
        }

        // Redirect for regular requests
        return redirect()->route('photo-diagnosis.index')->with('success', 'Analysis deleted successfully.');
    }



}
