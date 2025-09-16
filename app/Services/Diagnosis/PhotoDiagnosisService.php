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
        
        // Adaptive sampling based on image size
        $sampleSize = min(max(self::MIN_SAMPLE_SIZE, $totalPixels / 10), self::MAX_SAMPLE_SIZE);
        
        // Initialize analysis counters
        $colorAnalysis = $this->performColorAnalysis($image, $sampleSize);
        $textureAnalysis = $this->performTextureAnalysis($image, $sampleSize);
        $patternAnalysis = $this->performPatternAnalysis($image, $sampleSize);
        
        return [
            'color' => $colorAnalysis,
            'texture' => $textureAnalysis,
            'pattern' => $patternAnalysis,
            'sample_size' => $sampleSize,
            'image_quality' => $this->assessImageQuality($image)
        ];
    }

    /**
     * Get type-specific recommendations for analysis
     */
    private function getTypeSpecificRecommendations(string $analysisType, string $condition): array
    {
        if ($analysisType === 'leaves') {
            return $this->getLeavesRecommendations($condition);
        } else {
            return $this->getWatermelonRecommendations($condition);
        }
    }

    /**
     * Get leaves-specific recommendations
     */
    private function getLeavesRecommendations(string $condition): array
    {
        switch ($condition) {
            case 'Healthy Green Leaves':
                return [
                    '✅ Excellent! Your leaves show healthy green coloration with good structure',
                    '💧 Continue current watering practices: 1-1.5 inches per week, allowing soil to dry between waterings',
                    '🌱 Apply balanced fertilizer (10-10-10) every 4-6 weeks at rate of 1 pound per 100 square feet',
                    '☀️ Ensure 6-8 hours of direct sunlight daily - trim nearby plants if shading occurs',
                    '🔍 Monitor regularly for any changes in leaf color or texture',
                    '📸 For ongoing monitoring, upload clear, well-lit images of individual leaves'
                ];
            case 'Yellowing Leaves (Nutrient Deficiency)':
                return [
                    '⚠️ Yellowing indicates nutrient deficiency - immediate action required',
                    '💧 Check soil moisture: insert finger 2 inches deep - if wet, reduce watering frequency',
                    '🌱 Apply nitrogen-rich fertilizer (21-0-0) at rate of 1/2 cup per plant',
                    '🔍 Test soil pH: optimal range is 6.0-7.0 - if below 6.0, add 1 cup lime per plant',
                    '🍃 Remove severely yellowed leaves to redirect nutrients to healthy growth',
                    '📸 Monitor progress with follow-up photos in 1-2 weeks'
                ];
            case 'Yellowing with Disease Spots':
                return [
                    '🚨 Combined yellowing and spots indicate serious plant stress - urgent treatment needed',
                    '🧪 Apply copper-based fungicide (copper sulfate) at rate of 2 tablespoons per gallon of water',
                    '🍃 Remove all affected leaves immediately: cut at base, place in sealed bag',
                    '💧 Water only at soil level - avoid wetting leaves, use soaker hose or drip irrigation',
                    '🌱 Apply balanced fertilizer to strengthen plant immunity',
                    '📸 Monitor closely and upload follow-up photos in 3-5 days'
                ];
            case 'Spotted/Diseased Leaves':
                return [
                    '🚨 Spots indicate fungal/bacterial infection - urgent treatment needed within 24 hours',
                    '🧪 Apply copper-based fungicide (copper sulfate) at rate of 2 tablespoons per gallon of water',
                    '🍃 Remove all spotted leaves immediately: cut at base, place in sealed bag',
                    '💧 Water only at soil level - avoid wetting leaves, use soaker hose or drip irrigation',
                    '🌡️ Improve air circulation around plants to reduce humidity',
                    '📸 Monitor treatment progress with follow-up photos in 3-5 days'
                ];
            case 'Early Disease Symptoms':
                return [
                    '⚠️ Early disease symptoms detected - preventive action recommended',
                    '🧪 Apply preventive fungicide (neem oil) at rate of 2 tablespoons per gallon of water',
                    '🍃 Remove any visibly affected leaves to prevent spread',
                    '💧 Ensure proper drainage and avoid overwatering',
                    '🌡️ Improve air circulation and reduce plant density if crowded',
                    '📸 Monitor closely and upload follow-up photos in 1 week'
                ];
            case 'Unclear - Poor Image Quality':
                return [
                    '⚠️ Analysis limited due to poor image quality - please retry with better photo',
                    '📸 Use good lighting (natural daylight preferred)',
                    '🍃 Focus on individual leaves with clear detail',
                    '📱 Hold camera steady and ensure image is in focus',
                    '🔄 Try uploading a different angle or closer view',
                    '📞 Contact support if issues persist with clear images'
                ];
            default:
                return [
                    '⚠️ This is a simplified analysis due to processing limitations',
                    '📸 For more accurate results, try uploading a clearer, well-lit image of individual leaves',
                    '🔄 Consider trying the analysis again with a different photo',
                    '🍃 Ensure the image shows clear leaf details and good lighting'
                ];
        }
    }

    /**
     * Get watermelon-specific recommendations
     */
    private function getWatermelonRecommendations(string $condition): array
    {
        switch ($condition) {
            case 'Ripe Watermelon':
                return [
                    '🎉 Perfect! Your watermelon is ready for harvest - optimal timing achieved',
                    '🔊 Check ripeness: tap with knuckle - should produce hollow, deep sound (not dull thud)',
                    '👁️ Verify ground spot: should be creamy yellow (not white or green) - size of palm of hand',
                    '⏰ Harvest timing: pick in early morning (6-8 AM) when temperatures are coolest',
                    '🍉 Store harvested watermelon in cool, dry place for up to 2 weeks',
                    '📸 Great job! Your watermelon analysis shows excellent ripeness indicators'
                ];
            case 'Nearly Ripe Watermelon':
                return [
                    '⏳ Almost ready! Wait 3-7 more days for optimal sweetness and flavor',
                    '👁️ Monitor ground spot: should be transitioning from white to creamy yellow',
                    '🍃 Check tendrils: the one nearest the fruit should be 50-75% brown and dry',
                    '💧 Reduce watering: decrease from 1.5 inches to 1 inch per week to concentrate sugars',
                    '📸 For more accurate analysis, try uploading a clearer, well-lit image of the watermelon'
                ];
            case 'Unripe Watermelon':
                return [
                    '🌱 Still developing - patience will reward you with sweet, flavorful fruit',
                    '💧 Maintain consistent watering: 1.5-2 inches per week, never let soil dry completely',
                    '🌱 Fertilize properly: apply low-nitrogen fertilizer (5-10-10) at rate of 1/2 cup per plant every 3 weeks',
                    '🛡️ Pest protection: use floating row covers during early development, remove when flowers appear',
                    '📸 For more accurate analysis, try uploading a clearer, well-lit image of the watermelon'
                ];
            case 'Defective/Diseased Watermelon':
                return [
                    '🚨 Defects detected - immediate assessment and action required',
                    '🔍 Check for soft spots, cracks, or unusual odors - if present, harvest immediately',
                    '🍃 Remove affected fruit from the plant to prevent disease spread',
                    '💊 Apply appropriate fungicides if disease is confirmed',
                    '🌡️ Improve air circulation and reduce humidity around remaining fruits',
                    '📸 Monitor other watermelons closely and upload follow-up photos'
                ];
            default:
                return [
                    '⚠️ This is a simplified analysis due to processing limitations',
                    '📸 For more accurate results, try uploading a clearer, well-lit image of the watermelon',
                    '🔄 Consider trying the analysis again with a different photo',
                    '🍉 Ensure the image shows the watermelon clearly with good lighting'
                ];
        }
    }

    /**
     * Get urgency level based on condition and analysis type
     */
    private function getUrgencyLevel(string $condition, string $analysisType): string
    {
        if ($analysisType === 'leaves') {
            if (str_contains($condition, 'Spotted') || str_contains($condition, 'Diseased')) {
                return 'high';
            } elseif (str_contains($condition, 'Yellowing') || str_contains($condition, 'Wilted')) {
                return 'medium';
            }
        } else {
            if (str_contains($condition, 'Defective') || str_contains($condition, 'Diseased')) {
                return 'high';
            } elseif (str_contains($condition, 'Nearly Ripe')) {
                return 'medium';
            }
        }
        return 'low';
    }

    /**
     * Get treatment category based on condition and analysis type
     */
    private function getTreatmentCategory(string $condition, string $analysisType): string
    {
        if ($analysisType === 'leaves') {
            if (str_contains($condition, 'Spotted') || str_contains($condition, 'Diseased')) {
                return 'urgent_treatment';
            } elseif (str_contains($condition, 'Yellowing') || str_contains($condition, 'Wilted')) {
                return 'care';
            }
        } else {
            if (str_contains($condition, 'Defective') || str_contains($condition, 'Diseased')) {
                return 'urgent_treatment';
            } elseif (str_contains($condition, 'Ripe')) {
                return 'harvest';
            } elseif (str_contains($condition, 'Nearly Ripe')) {
                return 'monitoring';
            }
        }
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
    private function performColorAnalysis($image, int $sampleSize): array
    {
        $width = imagesx($image);
        $height = imagesy($image);
        
        $greenCount = 0;
        $yellowCount = 0;
        $brownCount = 0;
        $darkCount = 0;
        $redCount = 0;
        $whiteCount = 0;
        
        // Use stratified sampling for better coverage
        $samplesPerRow = max(1, floor(sqrt($sampleSize)));
        $samplesPerCol = max(1, floor($sampleSize / $samplesPerRow));
        
        for ($i = 0; $i < $samplesPerRow; $i++) {
            for ($j = 0; $j < $samplesPerCol; $j++) {
                $x = min($width - 1, floor(($i + 0.5) * $width / $samplesPerRow));
                $y = min($height - 1, floor(($j + 0.5) * $height / $samplesPerCol));
                
                $rgb = imagecolorat($image, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                
                $this->classifyPixelColor($r, $g, $b, $greenCount, $yellowCount, $brownCount, $darkCount, $redCount, $whiteCount);
            }
        }
        
        $totalSamples = $samplesPerRow * $samplesPerCol;
        
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
    private function performTextureAnalysis($image, int $sampleSize): array
    {
        $width = imagesx($image);
        $height = imagesy($image);
        
        $textureVariance = 0;
        $edgeCount = 0;
        $smoothAreas = 0;
        
        // Sample for texture analysis
        for ($i = 0; $i < min($sampleSize, 100); $i++) {
            $x = rand(1, $width - 2);
            $y = rand(1, $height - 2);
            
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
        }
        
        return [
            'variance' => $textureVariance / min($sampleSize, 100),
            'edge_density' => $edgeCount / min($sampleSize, 100),
            'smooth_areas' => $smoothAreas / min($sampleSize, 100)
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
    private function performPatternAnalysis($image, int $sampleSize): array
    {
        $width = imagesx($image);
        $height = imagesy($image);
        
        $spotCount = 0;
        $streakCount = 0;
        $uniformAreas = 0;
        
        // Sample for pattern analysis
        for ($i = 0; $i < min($sampleSize, 200); $i++) {
            $x = rand(0, $width - 1);
            $y = rand(0, $height - 1);
            
            if ($this->isSpotPattern($image, $x, $y)) {
                $spotCount++;
            }
            if ($this->isStreakPattern($image, $x, $y)) {
                $streakCount++;
            }
            if ($this->isUniformArea($image, $x, $y)) {
                $uniformAreas++;
            }
        }
        
        return [
            'spot_density' => $spotCount / min($sampleSize, 200),
            'streak_density' => $streakCount / min($sampleSize, 200),
            'uniform_areas' => $uniformAreas / min($sampleSize, 200)
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
        
        return max(0.1, min(1.0, $qualityScore));
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
        
        // Determine condition using enhanced logic
        $condition = $this->determineCondition($color, $texture, $pattern, $analysisType, $imageQuality);
        $confidence = $this->calculateConfidence($color, $texture, $pattern, $condition, $imageQuality);
        
        // Generate recommendations
        $recommendations = $this->getTypeSpecificRecommendations($analysisType, $condition);
        
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
            'condition_key' => strtolower(str_replace(' ', '_', $condition)),
            'confidence_score' => $confidence,
            'recommendations' => [
                'condition' => strtolower(str_replace(' ', '_', $condition)),
                'condition_label' => $condition,
                'recommendations' => $recommendations,
                'urgency_level' => $this->getUrgencyLevel($condition, $analysisType),
                'treatment_category' => $this->getTreatmentCategory($condition, $analysisType)
            ],
            'analysis_details' => [
                'analysis_method' => 'enhanced_multi_technique',
                'image_quality' => round($imageQuality, 2),
                'color_analysis' => $color,
                'texture_analysis' => $texture,
                'pattern_analysis' => $pattern,
                'disease_detected' => $this->isDiseaseDetected($condition),
                'confidence_factors' => $this->getConfidenceFactors($color, $texture, $pattern, $imageQuality)
            ],
            'image_metadata' => [
                'width' => $imageInfo[0],
                'height' => $imageInfo[1],
                'mime_type' => $imageInfo['mime'],
                'file_size' => $photo->getSize(),
                'quality_score' => round($imageQuality, 2)
            ]
        ];
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
    private function calculateConfidence(array $color, array $texture, array $pattern, string $condition, float $imageQuality): int
    {
        $confidence = 50; // Base confidence
        
        // Image quality factor
        $confidence += (int)($imageQuality * 30);
        
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
}