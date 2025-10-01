<?php

namespace App\Services\Diagnosis;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class PhotoDiagnosisService
{
    private const IMAGE_QUALITY_THRESHOLD = 0.7;
    private const MIN_SAMPLE_SIZE = 1000;
    private const MAX_SAMPLE_SIZE = 5000;
    
    /**
     * Analyze a photo for disease detection
     */
    public function analyze(UploadedFile $photo, string $analysisType): array
    {
        $startTime = microtime(true);
        
        Log::info('Starting enhanced photo diagnosis analysis', [
            'analysis_type' => $analysisType,
            'file_size' => $photo->getSize(),
            'file_type' => $photo->getMimeType()
        ]);

        try {
            $result = $this->performEnhancedAnalysis($photo, $analysisType);
            $result['processing_time'] = round(microtime(true) - $startTime, 2);
            return $result;
        } catch (\Exception $e) {
            Log::error('Photo diagnosis analysis failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'analysis_type' => $analysisType
            ]);
            
            return $this->getFallbackResult($analysisType);
        }
    }

    /**
     * Perform enhanced analysis with improved accuracy
     */
    private function performEnhancedAnalysis(UploadedFile $photo, string $analysisType): array
    {
        $imagePath = $photo->getRealPath();
        $imageInfo = getimagesize($imagePath);
        
        Log::info('Enhanced image processing started', [
            'image_path' => $imagePath,
            'image_info' => $imageInfo,
            'file_exists' => file_exists($imagePath),
            'file_size' => filesize($imagePath)
        ]);
        
        if (!$imageInfo) {
            throw new \RuntimeException('Unable to read image file');
        }

        // Load and preprocess image
        $image = $this->loadAndPreprocessImage($imagePath, $imageInfo);
        if (!$image) {
            throw new \RuntimeException('Failed to load and preprocess image');
        }
        
        $width = imagesx($image);
        $height = imagesy($image);
        $totalPixels = $width * $height;
        
        Log::info('Image preprocessed successfully', [
            'width' => $width,
            'height' => $height,
            'total_pixels' => $totalPixels
        ]);

        // Enhanced analysis with multiple techniques
        $analysisResults = $this->performMultiTechniqueAnalysis($image, $analysisType);
        
        imagedestroy($image);
        
        // Generate comprehensive results
        return $this->generateComprehensiveResults($analysisResults, $analysisType, $imageInfo, $photo);
    }

    /**
     * Load and preprocess image for better analysis
     */
    private function loadAndPreprocessImage(string $imagePath, array $imageInfo)
    {
        $image = null;
        switch ($imageInfo[2]) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($imagePath);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($imagePath);
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($imagePath);
                break;
        }

        if (!$image) {
            return null;
        }

        // Enhance image quality
        $image = $this->enhanceImageQuality($image);
        
        return $image;
    }

    /**
     * Enhance image quality for better analysis
     */
    private function enhanceImageQuality($image)
    {
        $width = imagesx($image);
        $height = imagesy($image);
        
        // Resize if too large for better processing
        if ($width > 1000 || $height > 1000) {
            $newWidth = min(1000, $width);
            $newHeight = min(1000, $height);
            $resized = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($image);
            $image = $resized;
        }
        
        // Apply slight contrast enhancement
        imagefilter($image, IMG_FILTER_CONTRAST, 10);
        
        return $image;
    }

    /**
     * Perform multi-technique analysis for better accuracy
     */
    private function performMultiTechniqueAnalysis($image, string $analysisType): array
    {
		$width = imagesx($image);
		$height = imagesy($image);
		$totalPixels = $width * $height;
		
		// Build a green-leaf mask (HSV-based) to focus analysis on plant tissue
		$maskSummary = $this->buildLeafMaskSummary($image);
		$leafCoverage = $maskSummary['coverage'];
		
		// Adaptive sampling based on image size
		$sampleSize = min(max(self::MIN_SAMPLE_SIZE, $totalPixels / 10), self::MAX_SAMPLE_SIZE);
		
		// Initialize analysis counters using the mask-aware samplers
		$colorAnalysis = $this->performColorAnalysis($image, $sampleSize, $maskSummary);
		$textureAnalysis = $this->performTextureAnalysis($image, $sampleSize, $maskSummary);
		$patternAnalysis = $this->performPatternAnalysis($image, $sampleSize, $maskSummary);
		
		return [
			'color' => $colorAnalysis,
			'texture' => $textureAnalysis,
			'pattern' => $patternAnalysis,
			'sample_size' => $sampleSize,
			'image_quality' => $this->assessImageQuality($image),
			'leaf_coverage' => $leafCoverage
		];
    }

    /**
     * Get type-specific recommendations for analysis
     */
    private function getTypeSpecificRecommendations(string $analysisType, string $condition): array
    {
        // Deprecated legacy path removed in favor of score-based engine
        return [];
    }

    /**
     * Get leaves-specific recommendations
     */
    private function getLeavesRecommendations(string $condition): array
    {
        // Deprecated legacy path removed in favor of score-based engine
        return [];
    }

    /**
     * Get watermelon-specific recommendations
     */
    private function getWatermelonRecommendations(string $condition): array
    {
        // Deprecated legacy path removed in favor of score-based engine
        return [];
    }

    /**
     * Get urgency level based on condition and analysis type
     */
    private function getUrgencyLevel(string $condition, string $analysisType): string
    {
        // Deprecated - urgency now computed from probabilities
        return 'low';
    }

    /**
     * Get treatment category based on condition and analysis type
     */
    private function getTreatmentCategory(string $condition, string $analysisType): string
    {
        // Deprecated - categories now derived from probabilities
        return 'maintenance';
    }

    /**
     * Get fallback analysis result when analysis fails
     */
    private function getFallbackResult(string $analysisType): array
    {
        $fallbackResults = [
            'leaves' => [
                'identified_type' => 'Unable to analyze - please try again',
                'identified_condition' => 'Unable to analyze - please try again',
                'condition_key' => 'analysis_failed',
                'confidence_score' => 0,
                'recommendations' => [
                    'condition' => 'analysis_failed',
                    'condition_label' => 'Analysis Failed',
                    'recommendations' => [
                        '⚠️ Analysis could not be completed for leaves',
                        '📸 Please ensure the image shows clear leaf details and good lighting',
                        '🍃 Try uploading a close-up photo of individual leaves',
                        '🔄 Consider trying the analysis again with a different photo',
                        '📞 Contact support if the issue persists'
                    ],
                    'urgency_level' => 'low',
                    'treatment_category' => 'maintenance'
                ],
                'analysis_details' => [
                    'error' => 'Image processing failed',
                    'fallback_used' => true
                ]
            ],
            'watermelon' => [
                'identified_type' => 'Unable to analyze - please try again',
                'identified_condition' => 'Unable to analyze - please try again',
                'condition_key' => 'analysis_failed',
                'confidence_score' => 0,
                'recommendations' => [
                    'condition' => 'analysis_failed',
                    'condition_label' => 'Analysis Failed',
                    'recommendations' => [
                        '⚠️ Analysis could not be completed for watermelon',
                        '📸 Please ensure the image shows the watermelon clearly with good lighting',
                        '🍉 Try uploading a photo with the watermelon in good lighting',
                        '🔄 Consider trying the analysis again with a different photo',
                        '📞 Contact support if the issue persists'
                    ],
                    'urgency_level' => 'low',
                    'treatment_category' => 'maintenance'
                ],
                'analysis_details' => [
                    'error' => 'Image processing failed',
                    'fallback_used' => true
                ]
            ]
        ];

        return $fallbackResults[$analysisType] ?? $fallbackResults['leaves'];
    }

    /**
     * Perform advanced color analysis
     */
	private function performColorAnalysis($image, int $sampleSize, ?array $maskSummary = null): array
    {
        $width = imagesx($image);
        $height = imagesy($image);
        
        $greenCount = 0;
        $yellowCount = 0;
        $brownCount = 0;
        $darkCount = 0;
        $redCount = 0;
        $whiteCount = 0;
		$validSamples = 0;
        
        // Use stratified sampling for better coverage
        $samplesPerRow = max(1, floor(sqrt($sampleSize)));
        $samplesPerCol = max(1, floor($sampleSize / $samplesPerRow));
        
        for ($i = 0; $i < $samplesPerRow; $i++) {
            for ($j = 0; $j < $samplesPerCol; $j++) {
				$x = min($width - 1, floor(($i + 0.5) * $width / $samplesPerRow));
				$y = min($height - 1, floor(($j + 0.5) * $height / $samplesPerCol));
				
				$consider = true;
				if ($maskSummary) {
					$consider = $this->isLeafPixelAt($image, $x, $y);
					if (!$consider) {
						$found = false;
						for ($radius = 1; $radius <= 3 && !$found; $radius++) {
							for ($dx = -$radius; $dx <= $radius && !$found; $dx++) {
								for ($dy = -$radius; $dy <= $radius && !$found; $dy++) {
									$nx = $x + $dx; $ny = $y + $dy;
									if ($nx >= 0 && $nx < $width && $ny >= 0 && $ny < $height && $this->isLeafPixelAt($image, $nx, $ny)) {
										$x = $nx; $y = $ny; $found = true; $consider = true;
									}
								}
							}
						}
						if (!$found) { $consider = false; }
					}
				}
				if (!$consider) { continue; }
				
				$rgb = imagecolorat($image, $x, $y);
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;
				
				$this->classifyPixelColor($r, $g, $b, $greenCount, $yellowCount, $brownCount, $darkCount, $redCount, $whiteCount);
				$validSamples++;
            }
        }
        
		$totalSamples = max(1, $validSamples);
        
        return [
            'green_ratio' => $greenCount / $totalSamples,
            'yellow_ratio' => $yellowCount / $totalSamples,
            'brown_ratio' => $brownCount / $totalSamples,
            'dark_ratio' => $darkCount / $totalSamples,
            'red_ratio' => $redCount / $totalSamples,
            'white_ratio' => $whiteCount / $totalSamples,
            'total_samples' => $totalSamples
        ];
    }

    /**
     * Classify pixel color with improved accuracy
     */
    private function classifyPixelColor(int $r, int $g, int $b, int &$greenCount, int &$yellowCount, 
                                      int &$brownCount, int &$darkCount, int &$redCount, int &$whiteCount): void
    {
        $brightness = ($r + $g + $b) / 3;
        $maxColor = max($r, $g, $b);
        $minColor = min($r, $g, $b);
        $colorRange = $maxColor - $minColor;
        $saturation = $colorRange / max(1, $maxColor);
        
        // Healthy green: dominant green with good brightness and moderate saturation
        if ($g > $r && $g > $b && $g > 100 && $brightness > 70 && $saturation > 0.2) {
            $greenCount++;
        }
        // Yellowing: yellow dominant with good brightness
        elseif ($r > $g && $g > $b && $r > 140 && $brightness > 90 && $saturation > 0.3) {
            $yellowCount++;
        }
        // Red/pink spots (rust, bacterial spots)
        elseif ($r > $g && $r > $b && $r > 120 && $brightness > 80) {
            $redCount++;
        }
        // White spots (powdery mildew, fungal growth)
        elseif ($r > 200 && $g > 200 && $b > 200 && $brightness > 200) {
            $whiteCount++;
        }
        // Dark spots/disease: various dark patterns
        elseif ($brightness < 80 || ($r < 100 && $g < 100 && $b < 100) || 
               ($r > $g && $g > $b && $r < 160 && $brightness < 100)) {
            $brownCount++;
        }
        // Very dark areas (necrosis, severe disease)
        elseif ($brightness < 50) {
            $darkCount++;
        }
    }

    /**
     * Perform texture analysis for disease patterns
     */
	private function performTextureAnalysis($image, int $sampleSize, ?array $maskSummary = null): array
    {
        $width = imagesx($image);
        $height = imagesy($image);
        
        $textureVariance = 0;
        $edgeCount = 0;
        $smoothAreas = 0;
		$valid = 0;
        
        // Sample for texture analysis
        for ($i = 0; $i < min($sampleSize, 100); $i++) {
            $x = rand(1, $width - 2);
            $y = rand(1, $height - 2);
			if ($maskSummary && !$this->isLeafPixelAt($image, $x, $y)) { $i--; continue; }
            
            // Calculate local variance
            $localVariance = $this->calculateLocalVariance($image, $x, $y);
            $textureVariance += $localVariance;
            
            // Detect edges
            if ($this->isEdgePixel($image, $x, $y)) {
                $edgeCount++;
            }
            
            // Detect smooth areas
            if ($localVariance < 50) {
                $smoothAreas++;
            }
			$valid++;
        }
        
        return [
			'variance' => $valid ? ($textureVariance / $valid) : 0,
			'edge_density' => $valid ? ($edgeCount / $valid) : 0,
			'smooth_areas' => $valid ? ($smoothAreas / $valid) : 0
        ];
    }

    /**
     * Calculate local variance around a pixel
     */
    private function calculateLocalVariance($image, int $x, int $y): float
    {
        $pixels = [];
        for ($dx = -1; $dx <= 1; $dx++) {
            for ($dy = -1; $dy <= 1; $dy++) {
                $px = $x + $dx;
                $py = $y + $dy;
                if ($px >= 0 && $px < imagesx($image) && $py >= 0 && $py < imagesy($image)) {
                    $rgb = imagecolorat($image, $px, $py);
                    $brightness = (($rgb >> 16) & 0xFF + ($rgb >> 8) & 0xFF + $rgb & 0xFF) / 3;
                    $pixels[] = $brightness;
                }
            }
        }
        
        if (count($pixels) < 3) return 0;
        
        $mean = array_sum($pixels) / count($pixels);
        $variance = 0;
        foreach ($pixels as $pixel) {
            $variance += pow($pixel - $mean, 2);
        }
        
        return $variance / count($pixels);
    }

    /**
     * Check if pixel is an edge
     */
    private function isEdgePixel($image, int $x, int $y): bool
    {
        if ($x <= 0 || $x >= imagesx($image) - 1 || $y <= 0 || $y >= imagesy($image) - 1) {
            return false;
        }
        
        $center = imagecolorat($image, $x, $y);
        $centerBrightness = (($center >> 16) & 0xFF + ($center >> 8) & 0xFF + $center & 0xFF) / 3;
        
        $neighbors = [
            imagecolorat($image, $x-1, $y-1), imagecolorat($image, $x, $y-1), imagecolorat($image, $x+1, $y-1),
            imagecolorat($image, $x-1, $y), imagecolorat($image, $x+1, $y),
            imagecolorat($image, $x-1, $y+1), imagecolorat($image, $x, $y+1), imagecolorat($image, $x+1, $y+1)
        ];
        
        $maxDiff = 0;
        foreach ($neighbors as $neighbor) {
            $neighborBrightness = (($neighbor >> 16) & 0xFF + ($neighbor >> 8) & 0xFF + $neighbor & 0xFF) / 3;
            $diff = abs($centerBrightness - $neighborBrightness);
            $maxDiff = max($maxDiff, $diff);
        }
        
        return $maxDiff > 30; // Threshold for edge detection
    }

    /**
     * Perform pattern analysis for disease detection
     */
	private function performPatternAnalysis($image, int $sampleSize, ?array $maskSummary = null): array
    {
        $width = imagesx($image);
        $height = imagesy($image);
        
        $spotCount = 0;
        $streakCount = 0;
        $uniformAreas = 0;
		$valid = 0;
        
        // Sample for pattern analysis
        for ($i = 0; $i < min($sampleSize, 200); $i++) {
            $x = rand(0, $width - 1);
            $y = rand(0, $height - 1);
			if ($maskSummary && !$this->isLeafPixelAt($image, $x, $y)) { $i--; continue; }
            
            if ($this->isSpotPattern($image, $x, $y)) {
                $spotCount++;
            }
            if ($this->isStreakPattern($image, $x, $y)) {
                $streakCount++;
            }
            if ($this->isUniformArea($image, $x, $y)) {
                $uniformAreas++;
            }
			$valid++;
        }
        
        return [
			'spot_density' => $valid ? ($spotCount / $valid) : 0,
			'streak_density' => $valid ? ($streakCount / $valid) : 0,
			'uniform_areas' => $valid ? ($uniformAreas / $valid) : 0
        ];
    }

    /**
     * Check for spot patterns (disease spots)
     */
    private function isSpotPattern($image, int $x, int $y): bool
    {
        if ($x < 2 || $x >= imagesx($image) - 2 || $y < 2 || $y >= imagesy($image) - 2) {
            return false;
        }
        
        $center = imagecolorat($image, $x, $y);
        $centerBrightness = (($center >> 16) & 0xFF + ($center >> 8) & 0xFF + $center & 0xFF) / 3;
        
        // Check if center is darker than surrounding area
        $surroundingBrightness = 0;
        $count = 0;
        for ($dx = -2; $dx <= 2; $dx++) {
            for ($dy = -2; $dy <= 2; $dy++) {
                if ($dx == 0 && $dy == 0) continue;
                $px = $x + $dx;
                $py = $y + $dy;
                if ($px >= 0 && $px < imagesx($image) && $py >= 0 && $py < imagesy($image)) {
                    $rgb = imagecolorat($image, $px, $py);
                    $surroundingBrightness += (($rgb >> 16) & 0xFF + ($rgb >> 8) & 0xFF + $rgb & 0xFF) / 3;
                    $count++;
                }
            }
        }
        
        if ($count == 0) return false;
        $surroundingBrightness /= $count;
        
        return $centerBrightness < $surroundingBrightness - 20; // Dark spot
    }

    /**
     * Check for streak patterns
     */
    private function isStreakPattern($image, int $x, int $y): bool
    {
        if ($x < 1 || $x >= imagesx($image) - 1 || $y < 1 || $y >= imagesy($image) - 1) {
            return false;
        }
        
        $center = imagecolorat($image, $x, $y);
        $centerBrightness = (($center >> 16) & 0xFF + ($center >> 8) & 0xFF + $center & 0xFF) / 3;
        
        // Check horizontal and vertical streaks
        $horizontalDiff = abs($centerBrightness - $this->getPixelBrightness($image, $x-1, $y)) + 
                         abs($centerBrightness - $this->getPixelBrightness($image, $x+1, $y));
        $verticalDiff = abs($centerBrightness - $this->getPixelBrightness($image, $x, $y-1)) + 
                       abs($centerBrightness - $this->getPixelBrightness($image, $x, $y+1));
        
        return $horizontalDiff > 40 || $verticalDiff > 40;
    }

    /**
     * Check for uniform areas
     */
    private function isUniformArea($image, int $x, int $y): bool
    {
        if ($x < 1 || $x >= imagesx($image) - 1 || $y < 1 || $y >= imagesy($image) - 1) {
            return false;
        }
        
        $center = imagecolorat($image, $x, $y);
        $centerBrightness = (($center >> 16) & 0xFF + ($center >> 8) & 0xFF + $center & 0xFF) / 3;
        
        $neighbors = [
            $this->getPixelBrightness($image, $x-1, $y-1), $this->getPixelBrightness($image, $x, $y-1), $this->getPixelBrightness($image, $x+1, $y-1),
            $this->getPixelBrightness($image, $x-1, $y), $this->getPixelBrightness($image, $x+1, $y),
            $this->getPixelBrightness($image, $x-1, $y+1), $this->getPixelBrightness($image, $x, $y+1), $this->getPixelBrightness($image, $x+1, $y+1)
        ];
        
        $maxDiff = 0;
        foreach ($neighbors as $neighbor) {
            $diff = abs($centerBrightness - $neighbor);
            $maxDiff = max($maxDiff, $diff);
        }
        
        return $maxDiff < 15; // Very uniform area
    }

    /**
     * Get pixel brightness safely
     */
    private function getPixelBrightness($image, int $x, int $y): float
    {
        if ($x < 0 || $x >= imagesx($image) || $y < 0 || $y >= imagesy($image)) {
            return 0;
        }
        
        $rgb = imagecolorat($image, $x, $y);
        return (($rgb >> 16) & 0xFF + ($rgb >> 8) & 0xFF + $rgb & 0xFF) / 3;
    }

    /**
     * Assess image quality
     */
    private function assessImageQuality($image): float
    {
        $width = imagesx($image);
        $height = imagesy($image);
        
        // Check for blur, noise, and overall quality
		$qualityScore = 1.0;
        
        // Penalize very small images
        if ($width < 200 || $height < 200) {
            $qualityScore *= 0.7;
        }
        
        // Check for excessive darkness
        $darkPixels = 0;
        $totalPixels = $width * $height;
        for ($i = 0; $i < min(100, $totalPixels); $i++) {
            $x = rand(0, $width - 1);
            $y = rand(0, $height - 1);
            $rgb = imagecolorat($image, $x, $y);
            $brightness = (($rgb >> 16) & 0xFF + ($rgb >> 8) & 0xFF + $rgb & 0xFF) / 3;
            if ($brightness < 50) {
                $darkPixels++;
            }
        }
        
        if ($darkPixels / min(100, $totalPixels) > 0.5) {
            $qualityScore *= 0.6;
        }
        
		// Slightly weigh in coverage of leaf tissue if available to avoid background bias
		$mask = $this->buildLeafMaskSummary($image);
		$coverage = $mask['coverage'];
		if ($coverage < 0.08) { $qualityScore *= 0.75; }
		elseif ($coverage < 0.15) { $qualityScore *= 0.9; }
		
		return max(0.1, min(1.0, $qualityScore));
    }

	/**
	 * Build a coarse HSV-based mask summary for leaf areas
	 */
	private function buildLeafMaskSummary($image): array
	{
		$width = imagesx($image);
		$height = imagesy($image);
		$sample = 0;
		$leaf = 0;
		$grid = max(20, (int)floor(min($width, $height) / 20));
		$stepX = max(1, (int)floor($width / $grid));
		$stepY = max(1, (int)floor($height / $grid));
		for ($x = 0; $x < $width; $x += $stepX) {
			for ($y = 0; $y < $height; $y += $stepY) {
				$rgb = imagecolorat($image, $x, $y);
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;
				if ($this->isLeafPixel($r, $g, $b)) { $leaf++; }
				$sample++;
			}
		}
		$coverage = $sample ? ($leaf / $sample) : 0.0;
		return [ 'coverage' => $coverage ];
	}

	private function isLeafPixelAt($image, int $x, int $y): bool
	{
		$rgb = imagecolorat($image, $x, $y);
		$r = ($rgb >> 16) & 0xFF;
		$g = ($rgb >> 8) & 0xFF;
		$b = $rgb & 0xFF;
		return $this->isLeafPixel($r, $g, $b);
	}

	private function isLeafPixel(int $r, int $g, int $b): bool
	{
		[$h, $s, $v] = $this->rgbToHsv($r, $g, $b);
		// Green hue band with moderate saturation and brightness
		return ($h >= 60 && $h <= 170) && ($s >= 0.22) && ($v >= 0.18);
	}

	private function rgbToHsv(int $r, int $g, int $b): array
	{
		$rN = $r / 255.0; $gN = $g / 255.0; $bN = $b / 255.0;
		$max = max($rN, $gN, $bN); $min = min($rN, $gN, $bN);
		$delta = $max - $min;
		$h = 0.0;
		if ($delta > 0) {
			if ($max === $rN) { $h = 60 * fmod((($gN - $bN) / $delta), 6); }
			elseif ($max === $gN) { $h = 60 * ((($bN - $rN) / $delta) + 2); }
			else { $h = 60 * ((($rN - $gN) / $delta) + 4); }
			if ($h < 0) { $h += 360; }
		}
		$s = $max == 0 ? 0 : ($delta / $max);
		$v = $max;
		return [$h, $s, $v];
	}

    /**
     * Generate comprehensive results based on all analyses
     */
	private function generateComprehensiveResults(array $analysisResults, string $analysisType, array $imageInfo, UploadedFile $photo): array
    {
        $color = $analysisResults['color'];
        $texture = $analysisResults['texture'];
        $pattern = $analysisResults['pattern'];
        $imageQuality = $analysisResults['image_quality'];
        $leafCoverage = $analysisResults['leaf_coverage'] ?? null;
        
        // Determine condition using enhanced logic
        $conditionScores = $this->computeConditionScores($color, $texture, $pattern, $imageQuality);
        // Pick the top-scoring condition
        arsort($conditionScores);
        $topKey = array_key_first($conditionScores);
        $condition = $this->conditionKeyToLabel($topKey);
        $confidence = $this->calculateConfidence($color, $texture, $pattern, $condition, $imageQuality, $leafCoverage);
        
		// Generate new recommendations based on condition probabilities
		// Add a per-analysis salt so outputs vary between analyses to avoid repetition
		$seedSalt = sha1(($photo->getPathname() ?? '') . '|' . $photo->getSize() . '|' . microtime(true) . '|' . random_int(0, 1_000_000_000));
		$recommendations = $this->buildRecommendationsFromScores($conditionScores, $analysisType, $seedSalt);
        
        Log::info('Enhanced analysis completed', [
            'analysis_type' => $analysisType,
            'condition' => $condition,
            'confidence' => $confidence,
            'image_quality' => $imageQuality,
            'color_analysis' => $color,
            'texture_analysis' => $texture,
                'pattern_analysis' => $pattern
        ]);

        return [
            'identified_type' => $condition,
            'identified_condition' => $condition,
            'condition_key' => $topKey,
            'confidence_score' => $confidence,
            'recommendations' => $recommendations,
            'condition_scores' => $conditionScores,
            'model_version' => 'melotech-vision-v2',
            'analysis_details' => [
                'analysis_method' => 'enhanced_multi_technique_masked',
                'image_quality' => round($imageQuality, 2),
                'color_analysis' => $color,
                'texture_analysis' => $texture,
                'pattern_analysis' => $pattern,
                'disease_detected' => $this->isDiseaseDetected($condition),
                'confidence_factors' => $this->getConfidenceFactors($color, $texture, $pattern, $imageQuality),
                'condition_scores' => $conditionScores,
                'leaf_coverage' => $leafCoverage
            ],
            'image_metadata' => [
                'width' => $imageInfo[0],
                'height' => $imageInfo[1],
                'mime_type' => $imageInfo['mime'],
                'file_size' => $photo->getSize(),
                'quality_score' => round($imageQuality, 2),
                'leaf_coverage' => $leafCoverage
            ]
        ];
    }

    /**
     * Compute five-condition probability scores (percentages)
     */
	private function computeConditionScores(array $color, array $texture, array $pattern, float $imageQuality): array
    {
        // Base scores derived from heuristics
        $scores = [
            'healthy' => 0.0,
            'fungal_infection' => 0.0,
            'nutrient_deficiency' => 0.0,
            'pest_damage' => 0.0,
            'viral_infection' => 0.0,
        ];

        // Healthy indicators (stronger emphasis when plant area is green and uniform)
        $scores['healthy'] += max(0.0, ($color['green_ratio'] - 0.30) * 4.0);
        $scores['healthy'] += max(0.0, ($pattern['uniform_areas'] ?? 0) * 2.0);
        $scores['healthy'] += max(0.0, ($texture['smooth_areas'] ?? 0) * 1.2);

        // Fungal: white powder, dark/brown spots, higher spot density
        $scores['fungal_infection'] += ($color['white_ratio'] ?? 0) * 4.0;
        $scores['fungal_infection'] += ($pattern['spot_density'] ?? 0) * 3.0;
        $scores['fungal_infection'] += ($color['brown_ratio'] ?? 0) * 2.0;

        // Nutrient deficiency: yellowing without many spots/edges
        $scores['nutrient_deficiency'] += ($color['yellow_ratio'] ?? 0) * 5.0;
        $scores['nutrient_deficiency'] += max(0.0, 1.0 - ($pattern['spot_density'] ?? 0) * 3.0);

        // Pest damage: red patches, edges, streaks
        $scores['pest_damage'] += ($color['red_ratio'] ?? 0) * 4.0;
        $scores['pest_damage'] += ($texture['edge_density'] ?? 0) * 3.0;
        $scores['pest_damage'] += ($pattern['streak_density'] ?? 0) * 2.0;

        // Viral infection: mottling/patchy color (variance + mixed ratios)
        $colorDiversity = ($color['green_ratio'] ?? 0) * ($color['yellow_ratio'] ?? 0) + ($color['white_ratio'] ?? 0) * 0.5;
        $scores['viral_infection'] += min(1.0, ($texture['variance'] ?? 0) / 250.0) * 3.0;
        $scores['viral_infection'] += $colorDiversity * 3.0;

		// Penalize healthy if clear disease indicators present (unless strong green uniformity)
		$diseaseIndicators = ($scores['fungal_infection'] + $scores['pest_damage'] + $scores['viral_infection']);
		if ($diseaseIndicators > 2.0 && !(($color['green_ratio'] ?? 0) > 0.75 && ($pattern['spot_density'] ?? 0) < 0.08)) {
			$scores['healthy'] *= 0.6;
		}

        // Image quality adjustment
        $qualityFactor = max(0.7, min(1.0, $imageQuality));
        foreach ($scores as $k => $v) {
            $scores[$k] = $v * $qualityFactor;
        }

        // Normalize to percentages
        $sum = array_sum($scores);
        if ($sum <= 0) {
            // Avoid division by zero - default to healthy moderate probability
            return [
                'healthy' => 40,
                'fungal_infection' => 15,
                'nutrient_deficiency' => 15,
                'pest_damage' => 15,
                'viral_infection' => 15,
            ];
        }

        $percentages = [];
        foreach ($scores as $k => $v) {
            $percentages[$k] = (int) round(($v / $sum) * 100);
        }

        // Ensure total 100 by adjusting the largest bucket
        $total = array_sum($percentages);
        if ($total !== 100) {
            arsort($percentages);
            $largestKey = array_key_first($percentages);
            $percentages[$largestKey] += 100 - $total;
        }

        return $percentages;
    }

    /**
     * Map condition key to human-readable label
     */
    private function conditionKeyToLabel(string $key): string
    {
        return match ($key) {
            'healthy' => 'Healthy',
            'fungal_infection' => 'Fungal Infection',
            'nutrient_deficiency' => 'Nutrient Deficiency',
            'pest_damage' => 'Pest Damage',
            'viral_infection' => 'Viral Infection',
            default => 'Unknown',
        };
    }

    /**
     * Determine condition using enhanced multi-technique analysis
     */
    private function determineCondition(array $color, array $texture, array $pattern, string $analysisType, float $imageQuality): string
    {
        if ($analysisType === 'leaves') {
            return $this->determineLeafCondition($color, $texture, $pattern, $imageQuality);
        } else {
            return $this->determineWatermelonCondition($color, $texture, $pattern, $imageQuality);
        }
    }

    /**
     * Determine leaf condition with enhanced accuracy
     */
    private function determineLeafCondition(array $color, array $texture, array $pattern, float $imageQuality): string
    {
        $diseaseScore = 0;
        $healthScore = 0;
        
        // Color-based disease indicators
        if ($color['brown_ratio'] > 0.12) $diseaseScore += 3;
        if ($color['dark_ratio'] > 0.06) $diseaseScore += 4;
        if ($color['red_ratio'] > 0.08) $diseaseScore += 2; // Rust, bacterial spots
        if ($color['white_ratio'] > 0.10) $diseaseScore += 3; // Powdery mildew
        
        // Texture-based indicators
        if ($texture['variance'] > 200) $diseaseScore += 2; // High texture variation
        if ($texture['edge_density'] > 0.3) $diseaseScore += 1; // Many edges (spots)
        
        // Pattern-based indicators
        if ($pattern['spot_density'] > 0.15) $diseaseScore += 3;
        if ($pattern['streak_density'] > 0.20) $diseaseScore += 2;
        
        // Health indicators
        if ($color['green_ratio'] > 0.45) $healthScore += 3;
        if ($texture['smooth_areas'] > 0.4) $healthScore += 1;
        if ($pattern['uniform_areas'] > 0.3) $healthScore += 1;
        
        // Yellowing indicators
        if ($color['yellow_ratio'] > 0.25) {
            if ($diseaseScore > $healthScore) {
                return 'Yellowing with Disease Spots';
            } else {
                return 'Yellowing Leaves (Nutrient Deficiency)';
            }
        }
        
        // Final determination
        if ($diseaseScore >= 6) {
            return 'Spotted/Diseased Leaves';
        } elseif ($diseaseScore >= 3) {
            return 'Early Disease Symptoms';
        } elseif ($healthScore >= 4) {
            return 'Healthy Green Leaves';
        } else {
            return 'Unclear - Poor Image Quality';
        }
    }

    /**
     * Determine watermelon condition with enhanced accuracy
     */
    private function determineWatermelonCondition(array $color, array $texture, array $pattern, float $imageQuality): string
    {
        $defectScore = 0;
        $ripenessScore = 0;
        
        // Defect indicators
        if ($color['brown_ratio'] > 0.10) $defectScore += 3;
        if ($color['dark_ratio'] > 0.05) $defectScore += 2;
        if ($pattern['spot_density'] > 0.12) $defectScore += 2;
        
        // Ripeness indicators
        if ($color['green_ratio'] > 0.4) $ripenessScore += 2; // Unripe
        if ($color['yellow_ratio'] > 0.15) $ripenessScore += 3; // Nearly ripe
        if ($texture['smooth_areas'] > 0.5) $ripenessScore += 1; // Smooth surface
        
        if ($defectScore >= 4) {
            return 'Defective/Diseased Watermelon';
        } elseif ($ripenessScore >= 4) {
            return 'Nearly Ripe Watermelon';
        } elseif ($ripenessScore >= 2) {
            return 'Unripe Watermelon';
        } else {
            return 'Ripe Watermelon';
        }
    }

    /**
     * Calculate confidence score based on multiple factors
     */
	private function calculateConfidence(array $color, array $texture, array $pattern, string $condition, float $imageQuality, ?float $leafCoverage = null): int
    {
        $confidence = 50; // Base confidence
        
        // Image quality factor
        $confidence += (int)($imageQuality * 30);
		// Leaf coverage factor (lower confidence if very low plant area)
		if ($leafCoverage !== null) {
			if ($leafCoverage < 0.10) $confidence -= 15;
			elseif ($leafCoverage < 0.20) $confidence -= 8;
			elseif ($leafCoverage > 0.50) $confidence += 5;
		}
        
        // Consistency factor
        $consistency = $this->calculateConsistency($color, $texture, $pattern);
        $confidence += (int)($consistency * 20);
        
        // Sample size factor
        $sampleSize = $color['total_samples'];
        if ($sampleSize > 1000) $confidence += 10;
        elseif ($sampleSize > 500) $confidence += 5;
        
        return min(95, max(30, $confidence));
    }

    /**
     * Calculate consistency across different analysis techniques
     */
    private function calculateConsistency(array $color, array $texture, array $pattern): float
    {
        $indicators = [
            $color['brown_ratio'] > 0.1,
            $color['dark_ratio'] > 0.05,
            $texture['variance'] > 150,
            $pattern['spot_density'] > 0.1
        ];
        
        $positiveCount = array_sum($indicators);
        $totalCount = count($indicators);
        
        // High consistency if most indicators agree
        return abs($positiveCount - $totalCount/2) / ($totalCount/2);
    }

    /**
     * Check if disease is detected
     */
    private function isDiseaseDetected(string $condition): bool
    {
        $diseaseKeywords = ['diseased', 'spotted', 'yellowing', 'defective', 'symptoms'];
        foreach ($diseaseKeywords as $keyword) {
            if (stripos($condition, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get confidence factors for transparency
     */
    private function getConfidenceFactors(array $color, array $texture, array $pattern, float $imageQuality): array
    {
        return [
            'image_quality_impact' => round($imageQuality * 100, 1) . '%',
            'color_consistency' => round($this->calculateConsistency($color, $texture, $pattern) * 100, 1) . '%',
            'analysis_techniques_used' => ['color_analysis', 'texture_analysis', 'pattern_analysis'],
			'sample_coverage' => $color['total_samples'] . ' pixels analyzed'
        ];
    }

    /**
     * Build structured recommendations from probability scores
     */
	private function buildRecommendationsFromScores(array $scores, string $analysisType, ?string $salt = null): array
    {
		arsort($scores);
		$topKey = array_key_first($scores);
		$topValue = (int)($scores[$topKey] ?? 0);

		$riskScore = (int)(($scores['fungal_infection'] ?? 0) + ($scores['pest_damage'] ?? 0) + ($scores['viral_infection'] ?? 0));
		$urgency = $riskScore >= 120 ? 'high' : ($riskScore >= 60 ? 'medium' : 'low');

		// Derive a seed from the scores and optional salt to vary per analysis
		$seedMaterial = json_encode([$scores, $salt ?? '']);
		$seed = abs(crc32($seedMaterial));
		mt_srand($seed);

		// Build large, type-specific recommendation pools programmatically (>=700 each type)
		$pools = $this->generateRecommendationPools($analysisType, $scores);

		// Compute treatment category
		$treatmentCategory = match ($topKey) {
			'healthy' => 'maintenance',
			'nutrient_deficiency' => 'nutrition',
			'fungal_infection' => 'fungicide_management',
			'pest_damage' => 'ipm_control',
			'viral_infection' => 'containment',
			default => 'maintenance',
		};

		// Build required UI-ready per-condition recommendations with dynamic actions from pools
		$emojiMap = [
			'healthy' => '🟢',
			'fungal_infection' => '🟤',
			'nutrient_deficiency' => '🟡',
			'pest_damage' => '🟥',
			'viral_infection' => '🟣',
		];

		$perCondition = [];
		$pickOne = function(array $pool): ?string { if (empty($pool)) { return null; } return $pool[array_rand($pool)]; };
		foreach (['healthy','fungal_infection','nutrient_deficiency','pest_damage','viral_infection'] as $key) {
			$actionText = $pickOne($pools[$key] ?? []) ?? 'Keep plants healthy with clean tools, even moisture, and regular checks.';
			$perCondition[] = [
				'key' => $key,
				'label' => $this->conditionKeyToLabel($key),
				'percent' => (int)($scores[$key] ?? 0),
				'emoji' => $emojiMap[$key] ?? '🟢',
				'action' => $actionText,
			];
		}

		// Build a dynamic overall recommendation based on top scoring conditions
		$sortedKeys = array_keys($scores); // $scores is already arsorted above
		$nonHealthy = array_values(array_filter($sortedKeys, function($k) use ($scores) {
			return $k !== 'healthy' && (($scores[$k] ?? 0) > 0);
		}));
		$snippets = [];
		$pickOne = function(array $pool): ?string {
			if (empty($pool)) { return null; }
			return $pool[array_rand($pool)];
		};

		if ($topKey === 'healthy') {
			$s1 = $pickOne($pools['healthy'] ?? []);
			if ($s1) { $snippets[] = $s1; }
			if (!empty($nonHealthy)) {
				$firstIssue = $nonHealthy[0];
				$s2 = $pickOne($pools[$firstIssue] ?? []);
				if ($s2) { $snippets[] = $s2; }
			}
		} else {
			// Leading with the top issue, then add a healthy maintenance line
			$s1 = $pickOne($pools[$topKey] ?? []);
			if ($s1) { $snippets[] = $s1; }
			$s2 = $pickOne($pools['healthy'] ?? []);
			if ($s2) { $snippets[] = $s2; }
			// Optionally blend second issue if significant
			if (isset($sortedKeys[1]) && ($scores[$sortedKeys[1]] ?? 0) >= 10) {
				$secondKey = $sortedKeys[1];
				if ($secondKey !== 'healthy') {
					$s3 = $pickOne($pools[$secondKey] ?? []);
					if ($s3) { $snippets[] = $s3; }
				}
			}
		}

		$snippets = array_values(array_filter($snippets));
		$overallText = '';
		if (!empty($snippets)) {
			$overallText = 'Overall: ' . $snippets[0];
			if (isset($snippets[1])) { $overallText .= ' Also, ' . $snippets[1]; }
		}

		// Build a varied, non-repeating mixed list weighted by condition percentages
		$totalTipsTarget = 6; // concise output with variety
		$conditions = ['healthy','nutrient_deficiency','fungal_infection','pest_damage','viral_infection'];
		$selected = [];
		$used = [];
		$weights = [];
		foreach ($conditions as $c) { $weights[$c] = max(1, (int)($scores[$c] ?? 0)); }
		$draw = function(array $pool, array &$used) {
			$pool = array_values(array_diff($pool, $used));
			if (empty($pool)) { return null; }
			$pick = $pool[array_rand($pool)];
			$used[] = $pick;
			return $pick;
		};
		// Ensure top condition contributes at least 2 tips
		$mainPool = $pools[$topKey] ?? [];
		if (!empty($mainPool)) {
			for ($i = 0; $i < 2 && count($selected) < $totalTipsTarget; $i++) {
				$pick = $draw($mainPool, $used);
				if ($pick) { $selected[] = $pick; }
			}
		}
		// Fill remaining by weighted round-robin without replacement
		while (count($selected) < $totalTipsTarget) {
			$sum = array_sum($weights);
			if ($sum <= 0) { break; }
			$r = mt_rand(1, $sum);
			$acc = 0;
			$chosen = $conditions[0];
			foreach ($conditions as $c) { $acc += $weights[$c]; if ($r <= $acc) { $chosen = $c; break; } }
			$pick = $draw($pools[$chosen] ?? [], $used);
			if ($pick) { $selected[] = $pick; }
			$weights[$chosen] = max(0, $weights[$chosen] - 10);
		}
		$selected = array_values(array_unique($selected));
		$selected = array_slice($selected, 0, $totalTipsTarget);

		return [
			'condition' => $topKey,
			'condition_label' => $this->conditionKeyToLabel($topKey),
			'urgency_level' => $urgency,
			'treatment_category' => $treatmentCategory,
			'by_condition' => [
				'healthy' => $scores['healthy'] ?? 0,
				'fungal_infection' => $scores['fungal_infection'] ?? 0,
				'nutrient_deficiency' => $scores['nutrient_deficiency'] ?? 0,
				'pest_damage' => $scores['pest_damage'] ?? 0,
				'viral_infection' => $scores['viral_infection'] ?? 0,
			],
			// New UI-ready structure
			'per_condition' => $perCondition,
			'overall' => $overallText,
			'overall_percent' => $topValue,
			// Legacy list kept for other screens if any
			'recommendations' => $selected,
		];
	}

	private function generateRecommendationPools(string $analysisType, array $scores): array
	{
		// Lightweight templating to produce a large set of simple, practical, unique lines.
		// We vary verbs, measures, intervals, and add percentage-aware phrases.
		$percent = function(string $k): int { return max(0, min(100, (int)($k))); };
		$hp = $percent($scores['healthy'] ?? 0);
		$np = $percent($scores['nutrient_deficiency'] ?? 0);
		$fp = $percent($scores['fungal_infection'] ?? 0);
		$pp = $percent($scores['pest_damage'] ?? 0);
		$vp = $percent($scores['viral_infection'] ?? 0);

		$isLeaves = ($analysisType === 'leaves');

		$verbsWater = ['Keep','Maintain','Adjust','Check','Inspect','Clean','Improve','Reduce','Increase','Record','Note','Plan','Watch','Monitor','Avoid','Use','Set','Ensure','Support','Protect'];
		$verbsLeaf = ['Remove','Cut','Isolate','Check','Feed','Mulch','Water','Thin','Tie','Clean','Bag','Throw','Support','Shade','Cover','Open','Lift','Wipe','Spray','Pinch','Trim','Prune','Dust'];
		$freqs = ['today','every 3 days','weekly','twice per week','after rain','every 10 days','every 2 weeks','every 4 days','every 5 days','every 8 days','after watering','before sunset'];
		$amountsIrr = ['lightly','moderately','deeply'];
		$unitsFert = ['g/plant','kg/ha'];
		$timeQualifiers = ['in the morning','in the evening','at sunset','before noon','on cool days'];
		$locQualifiers = ['per row','per bed','per plant','around roots','along the drip line'];
		// Use tails directly as complete recommendations
		$useTailsDirectly = function(array $tails) {
			return $tails;
		};

		// Healthy (simple, practical, action-first)
		$healthyTails = [
			'water at the base; do not wet leaves',
			'give 6–8 hours of sun each day',
			'keep soil evenly moist; add 2–4 cm mulch',
			'open crowded plants to let air move',
			'pull weeds near the stems',
			'clean tools before and after work',
			'check color and growth; note changes',
			'fix low spots that hold water',
			'keep pets and livestock away from beds',
			'watch for early spots or holes',
			'lay drip lines straight for even flow',
			'shade young plants on very hot days',
			'keep walkways dry to reduce humidity',
			'add compost thinly around the plants',
			'cover soil, not leaves, when you water',
			'place labels for date and variety',
			'pick up and discard plant trash',
			'fix broken stakes and ties at once',
			'check leaf undersides for any changes',
			'keep a simple log of work and weather',
			'flush salts with a deep watering if tips burn',
			'keep mulch off stems to prevent rot',
			'avoid stepping on beds to keep soil loose',
			'rotate crops each season to prevent disease buildup',
			'plant companion flowers to attract beneficial insects',
			'use organic matter to improve soil structure',
			'monitor soil pH and adjust if needed',
			'provide wind protection for young seedlings',
			'harvest regularly to encourage new growth',
			'store seeds properly in cool, dry places',
			'plan garden layout for maximum sun exposure',
			'use row covers to protect from frost',
			'practice crop rotation every 2-3 years',
			'build raised beds for better drainage',
			'install trellises for climbing plants',
			'create pathways between rows for easy access',
			'use natural pest deterrents like marigolds',
			'water deeply but less frequently',
			'remove diseased plants immediately',
			'keep garden clean and weed-free',
			'use organic fertilizers when possible',
			'plant at the right time for your zone',
			'provide adequate spacing between plants',
			'use quality seeds from reliable sources',
			'monitor weather and adjust care accordingly',
			'keep records of what works best',
			'learn about your specific plant varieties',
			'join local gardening groups for advice',
			'experiment with different growing methods',
			'celebrate successes and learn from failures',
		];
		$healthy = $useTailsDirectly($healthyTails);

		// Nutrient deficiency (clear feeding and care steps)
        $nutrientTails = [
            'Put compost around roots and cover with leaves or straw',
            'Give balanced plant food as instructed',
            'Water after feeding; keep soil slightly wet',
            'Look at new leaves; should be greener in a week',
            'Do not water too much; keep routine',
            'Add ash or lime only if soil is too sour',
            'Mix a little organic feed into soil and water',
            'Cover soil lightly to keep nutrients from washing away',
            'Check soil if leaves stay yellow',
            'Feed weak plants a little; do again if needed',
            'Test soil pH and adjust to 6.0-7.0 range',
            'Add bone meal for phosphorus deficiency',
            'Use fish emulsion for quick nitrogen boost',
            'Apply seaweed extract for trace minerals',
            'Mix coffee grounds into soil for nitrogen',
            'Add eggshells for calcium deficiency',
            'Use banana peels for potassium boost',
            'Apply Epsom salt for magnesium deficiency',
            'Water with compost tea weekly',
            'Add worm castings around plant base',
            'Use slow-release fertilizer for steady feeding',
            'Check for iron deficiency with yellow leaves',
            'Add sulfur to lower soil pH if needed',
            'Use blood meal for severe nitrogen deficiency',
            'Apply rock phosphate for phosphorus needs',
            'Mix greensand for potassium and trace minerals',
            'Use alfalfa meal as organic fertilizer',
            'Apply liquid kelp for micronutrients',
            'Check drainage to prevent nutrient leaching',
            'Mulch with grass clippings for nitrogen',
            'Use aged manure for complete nutrition',
            'Apply foliar feeding for quick absorption',
            'Test soil every 2-3 years',
            'Rotate heavy feeders with light feeders',
            'Plant nitrogen-fixing cover crops',
            'Use organic matter to improve soil structure',
            'Avoid over-fertilizing which can burn roots',
            'Water deeply to help nutrients reach roots',
            'Monitor plant response to feeding',
            'Keep feeding schedule consistent',
            'Adjust fertilizer based on plant growth stage',
        ];
        

		$nutrient = $useTailsDirectly($nutrientTails);

		// Fungal infection (hygiene, airflow, dry leaves)
		$fungalTails = [
			'Remove the worst sick leaves; bag and throw away',
			'Water in the morning; keep leaves dry',
			'Open plant spacing; let air move through',
			'Clean hands and tools before touching other plants',
			'Check leaves after rain and remove new spots',
			'Avoid overhead watering; use drip or water at base',
			'Keep mulch off the stems to prevent rot',
			'Lift a few inner leaves to let sun reach wet areas',
			'Pick and discard leaves with white powder',
			'Work on healthy plants first; sick plants last',
			'Apply copper fungicide to affected areas',
			'Use baking soda spray on infected leaves',
			'Remove all fallen leaves from ground',
			'Improve air circulation around plants',
			'Water only at soil level, never on leaves',
			'Space plants wider to reduce humidity',
			'Use fans to increase air movement',
			'Apply neem oil as natural fungicide',
			'Remove infected plant parts immediately',
			'Disinfect tools with bleach solution',
			'Plant disease-resistant varieties next season',
			'Rotate crops to prevent soil-borne fungi',
			'Use drip irrigation instead of sprinklers',
			'Remove weeds that harbor fungal spores',
			'Apply sulfur dust to prevent powdery mildew',
			'Use milk spray for powdery mildew control',
			'Remove and destroy severely infected plants',
			'Improve soil drainage to reduce moisture',
			'Apply compost tea to boost plant immunity',
			'Use row covers to protect from rain',
			'Prune to improve air circulation',
			'Apply hydrogen peroxide solution to soil',
			'Use chamomile tea as natural fungicide',
			'Remove dead plant material regularly',
			'Apply cinnamon powder to cut stems',
			'Use garlic spray for fungal prevention',
			'Improve garden hygiene and cleanliness',
			'Apply beneficial fungi to compete with pathogens',
			'Use proper plant spacing for air flow',
			'Monitor humidity levels in greenhouse',
			'Apply potassium bicarbonate spray',
			'Remove infected soil and replace',
			'Use resistant rootstocks when available',
			'Apply seaweed extract to strengthen plants',
			'Use proper watering techniques',
			'Monitor plants daily for early signs',
			'Apply organic fungicides as prevention',
			'Keep garden tools clean and disinfected',
			'Use proper plant nutrition to prevent stress',
		];
		$fungal = $useTailsDirectly($fungalTails);

		// Pest damage (observe, remove, clean)
		$pestTails = [
			'Pick off visible pests by hand; bag and throw away',
			'Wash leaves with a gentle water spray in the morning',
			'Remove weeds and plant trash where pests hide',
			'Check the undersides of leaves for eggs and small insects',
			'Keep the area clean; do not leave fallen sick leaves',
			'Trap crawling pests with simple sticky cards near rows',
			'Shake plants gently and look for small insects that fall',
			'Place light-colored boards to spot moving pests easily',
			'Block ant trails to reduce sap-sucking insects',
			'Limit night lights near the plot to avoid moths',
			'Use neem oil spray to repel many pests',
			'Apply diatomaceous earth around plant bases',
			'Introduce beneficial insects like ladybugs',
			'Use floating row covers to exclude pests',
			'Plant marigolds to repel nematodes',
			'Apply insecticidal soap for soft-bodied pests',
			'Use beer traps for slugs and snails',
			'Hand-pick large pests like caterpillars',
			'Apply Bacillus thuringiensis for caterpillar control',
			'Use yellow sticky traps for flying insects',
			'Plant herbs like basil to repel aphids',
			'Apply garlic spray to deter many pests',
			'Use companion planting to confuse pests',
			'Remove and destroy heavily infested plants',
			'Apply horticultural oil in dormant season',
			'Use pheromone traps for specific moth species',
			'Plant trap crops to lure pests away',
			'Apply spinosad for caterpillar and thrip control',
			'Use beneficial nematodes for soil pests',
			'Apply kaolin clay to deter many insects',
			'Use reflective mulch to confuse flying pests',
			'Plant diverse crops to reduce pest buildup',
			'Apply pyrethrin for quick pest knockdown',
			'Use row covers during peak pest season',
			'Apply essential oil sprays for pest control',
			'Use physical barriers like collars around stems',
			'Apply iron phosphate for slug control',
			'Use beneficial fungi to control pest larvae',
			'Plant flowers to attract beneficial insects',
			'Apply dormant oil spray in winter',
			'Use copper tape to deter slugs',
			'Apply azadirachtin for broad-spectrum control',
			'Use vacuum cleaner to remove small pests',
			'Apply mineral oil for scale insect control',
			'Use aluminum foil mulch to repel aphids',
			'Apply spinetoram for caterpillar control',
			'Use beneficial bacteria for pest control',
			'Apply horticultural soap for soft pests',
			'Use exclusion netting for bird protection',
			'Apply botanical insecticides as last resort',
			'Use integrated pest management approach',
			'Monitor pest populations regularly',
			'Apply treatments at optimal timing',
		];
		$pest = $useTailsDirectly($pestTails);

		// Viral infection (limit spread, handle last)
        $viralTails = [
            'Keep sick plants away from healthy ones',
            'Touch healthy plants first, sick plants last',
            'Remove badly spotted leaves; do not compost',
            'Wash hands and tools before moving to next row',
            'Plant in a new spot if many get sick',
            'Control pests that suck plant sap',
            'Do not save seeds from sick plants',
            'Keep field edges clean to stop pests',
            'Bag and throw away removed plant parts',
            'Limit visitors during sickness outbreaks',
            'Disinfect tools with bleach between plants',
            'Use virus-free certified seeds and plants',
            'Control aphids and whiteflies that spread viruses',
            'Remove infected plants immediately and destroy',
            'Do not touch healthy plants after infected ones',
            'Use row covers to exclude virus-carrying insects',
            'Plant resistant varieties when available',
            'Avoid working in wet conditions',
            'Clean greenhouse surfaces regularly',
            'Use separate tools for infected areas',
            'Monitor for early virus symptoms',
            'Remove weeds that harbor viruses',
            'Use reflective mulch to repel aphids',
            'Apply insecticidal soap to control vectors',
            'Plant trap crops to lure away pests',
            'Use beneficial insects to control vectors',
            'Avoid over-fertilizing which attracts pests',
            'Keep plants healthy to resist infection',
            'Use proper plant spacing for air circulation',
            'Apply neem oil to deter virus vectors',
            'Remove and destroy all infected material',
            'Use virus-free rootstocks for grafting',
            'Control ants that protect aphids',
            'Use yellow sticky traps for whiteflies',
            'Apply systemic insecticides for vector control',
            'Plant diverse crops to reduce virus spread',
            'Use proper irrigation to avoid stress',
            'Monitor for vector insects regularly',
            'Apply horticultural oil to smother vectors',
            'Use resistant cultivars when possible',
            'Practice strict sanitation in greenhouse',
            'Remove volunteer plants that may carry viruses',
            'Use proper crop rotation to break cycles',
            'Apply beneficial nematodes for soil pests',
            'Use physical barriers to exclude vectors',
            'Monitor weather conditions that favor vectors',
            'Apply treatments at optimal timing',
            'Keep detailed records of virus outbreaks',
            'Use integrated pest management approach',
            'Consult extension service for identification',
        ];
        
		$viral = $useTailsDirectly($viralTails);

		// Watermelon-specific simple tasks (fruit handling and hygiene)
		$melonExtras = [];
		if (!$isLeaves) {
			$melonTails = [
				'Lay straw or cardboard under fruits to keep them dry',
				'Adjust water in the morning; keep fruit skin dry',
				'Avoid bruising fruits; handle gently during weeding',
				'Keep vines spaced for airflow; reduce humidity',
				'Harvest pick only when the side touching soil is creamy',
				'Turn fruits weekly to avoid rot spots',
				'Shade young fruits during very hot midday sun',
				'Inspect fruit surface for lesions or cracks after rain',
				'Clean harvest tools; keep them dry',
				'Record size, color, and any defects each week',
				'Label near rows for dates and notes',
				'Wash handle fruit gently to avoid skin damage',
				'Rotate fruit off bare soil to reduce rot',
				'Move leaves away from sitting on wet fruits',
				'Remove old and rotting fruits from the field fast',
				'Support heavy fruits with slings or hammocks',
				'Check for hollow sound when tapping ripe fruits',
				'Look for yellow spot where fruit touches ground',
				'Monitor fruit size and growth rate weekly',
				'Protect fruits from birds with netting',
				'Keep fruits elevated above ground level',
				'Check for cracks or splits in fruit skin',
				'Harvest in early morning for best quality',
				'Store harvested fruits in cool, dry place',
				'Discard fruits with soft spots or mold',
				'Use clean, sharp knife for harvesting',
				'Handle fruits by the stem, not the body',
				'Check for insect damage on fruit surface',
				'Rotate fruits to prevent pressure points',
				'Keep harvest area clean and organized',
				'Monitor sugar content with refractometer',
				'Check for proper fruit shape and size',
				'Look for uniform color development',
				'Test fruit firmness before harvesting',
				'Keep detailed harvest records',
				'Sort fruits by size and quality',
				'Pack fruits carefully to prevent bruising',
				'Label containers with harvest date',
				'Store fruits at proper temperature',
				'Check for internal quality before storage',
				'Discard fruits with internal defects',
				'Use proper containers for transport',
				'Keep fruits away from ethylene sources',
				'Monitor storage conditions regularly',
				'Check for signs of spoilage daily',
				'Rotate stored fruits regularly',
				'Use first-in-first-out inventory system',
				'Keep storage area clean and dry',
				'Monitor temperature and humidity levels',
				'Check for pest damage in storage',
				'Use proper handling techniques',
				'Train workers on proper fruit handling',
				'Keep harvest equipment clean',
				'Use appropriate protective equipment',
				'Follow food safety guidelines',
				'Document all harvest activities',
				'Maintain quality standards throughout',
			];
			$melonExtras = $useTailsDirectly($melonTails);
		}

		// Expand well beyond 700 by combining with frequency and simple qualifiers
		$decorate = function(array $lines) use ($freqs, $timeQualifiers, $locQualifiers) {
			$out = [];
			foreach ($lines as $line) {
				foreach ($freqs as $f) {
					$out[] = $line . ' (' . $f . ')';
					$out[] = $line . ' (' . $f . ', ' . $timeQualifiers[array_rand($timeQualifiers)] . ')';
					$out[] = $line . ' (' . $f . ', ' . $locQualifiers[array_rand($locQualifiers)] . ')';
				}
			}
			return $out;
		};

		$healthy = $decorate($healthy);
		$nutrient = $decorate($nutrient);
		$fungal = $decorate($fungal);
		$pest = $decorate($pest);
		$viral = $decorate($viral);
		if (!$isLeaves) { $melonExtras = $decorate($melonExtras); }

		// De-duplicate to keep language clean and simple; no artificial padding
		$uniqueList = function(array $arr): array { return array_values(array_unique($arr)); };

		$healthy = $uniqueList($healthy);
		$nutrient = $uniqueList($nutrient);
		$fungal = $uniqueList($fungal);
		$pest = $uniqueList($pest);
		$viral = $uniqueList($viral);

		if (!$isLeaves) {
			// For watermelon, blend extras primarily into healthy and pest/fungal sets
			$healthy = array_values(array_unique(array_merge($healthy, array_slice($melonExtras, 0, 240))));
			$pest = array_values(array_unique(array_merge($pest, array_slice($melonExtras, 0, 240))));
		}

		return [
			'healthy' => $healthy,
			'nutrient_deficiency' => $nutrient,
			'fungal_infection' => $fungal,
			'pest_damage' => $pest,
			'viral_infection' => $viral,
		];
	}

    private function rangeFromProb(int $probability, float $min, float $max): string
    {
        $p = max(0, min(100, $probability));
        $value = $min + ($max - $min) * ($p / 100.0);
        // Round sensibly depending on scale
        if ($max <= 3) {
            return number_format($value, 1);
        }
        if ($max <= 10) {
            return (string) round($value, 0);
        }
        return (string) round($value, 0);
    }
}