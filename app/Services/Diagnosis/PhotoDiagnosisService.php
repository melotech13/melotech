<?php

namespace App\Services\Diagnosis;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class PhotoDiagnosisService
{
    /**
     * Analyze a photo for disease detection
     */
    public function analyze(UploadedFile $photo, string $analysisType): array
    {
        Log::info('Starting photo diagnosis analysis', [
            'analysis_type' => $analysisType,
            'file_size' => $photo->getSize(),
            'file_type' => $photo->getMimeType()
        ]);

        try {
            return $this->performAnalysis($photo, $analysisType);
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
     * Perform the actual analysis
     */
    private function performAnalysis(UploadedFile $photo, string $analysisType): array
    {
        // Basic image analysis using simple color detection
        $imagePath = $photo->getRealPath();
        $imageInfo = getimagesize($imagePath);
        
        Log::info('Image processing started', [
            'image_path' => $imagePath,
            'image_info' => $imageInfo,
            'file_exists' => file_exists($imagePath),
            'file_size' => filesize($imagePath)
        ]);
        
        if (!$imageInfo) {
            throw new \RuntimeException('Unable to read image file');
        }

        // Load image
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
            throw new \RuntimeException('Failed to load image');
        }
        
        Log::info('Image loaded successfully', [
            'width' => imagesx($image),
            'height' => imagesy($image),
            'image_type' => $imageInfo[2]
        ]);

        // Simple color analysis
        $width = imagesx($image);
        $height = imagesy($image);
        $totalPixels = $width * $height;
        
        // Sample more pixels for better analysis
        $sampleSize = min(500, $totalPixels);
        $greenCount = 0;
        $yellowCount = 0;
        $brownCount = 0;
        $darkCount = 0;
        
        for ($i = 0; $i < $sampleSize; $i++) {
            $x = rand(0, $width - 1);
            $y = rand(0, $height - 1);
            $rgb = imagecolorat($image, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            
            // Improved color classification for better disease detection
            $brightness = ($r + $g + $b) / 3;
            $maxColor = max($r, $g, $b);
            $minColor = min($r, $g, $b);
            $colorRange = $maxColor - $minColor;
            
            // Healthy green: dominant green with good brightness and low color variation
            if ($g > $r && $g > $b && $g > 110 && $brightness > 75 && $colorRange < 55) {
                $greenCount++;
            }
            // Yellowing: yellow dominant with moderate brightness
            elseif ($r > $g && $g > $b && $r > 145 && $brightness > 95) {
                $yellowCount++;
            }
            // Dark spots/disease: moderate sensitivity for disease detection
            elseif ($brightness < 85 || ($r < 110 && $g < 110 && $b < 110) || 
                   ($r > $g && $g > $b && $r < 170 && $brightness < 110)) {
                $brownCount++;
            }
            // Very dark areas (necrosis, severe disease)
            elseif ($brightness < 55) {
                $darkCount++;
            }
        }
        
        imagedestroy($image);
        
        // Determine condition based on improved analysis
        $greenRatio = $greenCount / $sampleSize;
        $yellowRatio = $yellowCount / $sampleSize;
        $brownRatio = $brownCount / $sampleSize;
        $darkRatio = $darkCount / $sampleSize;
        
        if ($analysisType === 'leaves') {
            // Balanced analysis - detect both healthy and diseased leaves properly
            $totalDarkness = $brownRatio + $darkRatio;
            
            Log::info('Balanced analysis starting', [
                'brown_ratio' => $brownRatio,
                'dark_ratio' => $darkRatio,
                'green_ratio' => $greenRatio,
                'yellow_ratio' => $yellowRatio,
                'total_darkness' => $totalDarkness
            ]);
            
            // Check for disease first - moderate sensitivity
            if ($brownRatio > 0.15 || $darkRatio > 0.08 || $totalDarkness > 0.20) {
                $condition = 'Spotted/Diseased Leaves';
                $confidence = 80;
                Log::info('Disease detected', [
                    'reason' => 'brown_ratio > 0.15 OR dark_ratio > 0.08 OR total_darkness > 0.20',
                    'brown_ratio' => $brownRatio,
                    'dark_ratio' => $darkRatio,
                    'total_darkness' => $totalDarkness
                ]);
            } elseif ($yellowRatio > 0.30) {
                $condition = 'Yellowing Leaves';
                $confidence = 75;
                Log::info('Yellowing detected', ['yellow_ratio' => $yellowRatio]);
            } elseif ($greenRatio > 0.50) {
                $condition = 'Healthy Green Leaves';
                $confidence = 80;
                Log::info('Healthy detected', ['green_ratio' => $greenRatio]);
            } else {
                // If unclear, check for any dark areas
                if ($totalDarkness > 0.10) {
                    $condition = 'Spotted/Diseased Leaves';
                    $confidence = 65;
                    Log::info('Disease detected (unclear case)', ['total_darkness' => $totalDarkness]);
                } else {
                    $condition = 'Healthy Green Leaves';
                    $confidence = 70;
                    Log::info('Healthy detected (unclear case)', ['total_darkness' => $totalDarkness]);
                }
            }
        } else {
            // Watermelon analysis - improved defect detection
            if ($brownRatio > 0.15) {
                $condition = 'Defective/Diseased Watermelon';
                $confidence = 70;
            } elseif ($greenRatio > 0.5) {
                $condition = 'Unripe Watermelon';
                $confidence = 55;
            } elseif ($yellowRatio > 0.2) {
                $condition = 'Nearly Ripe Watermelon';
                $confidence = 50;
            } else {
                $condition = 'Ripe Watermelon';
                $confidence = 45;
            }
        }

        // Generate type-specific recommendations
        $recommendations = $this->getTypeSpecificRecommendations($analysisType, $condition);

        Log::info('Analysis completed', [
            'analysis_type' => $analysisType,
            'condition' => $condition,
            'confidence' => $confidence,
            'green_ratio' => round($greenRatio, 2),
            'yellow_ratio' => round($yellowRatio, 2),
            'brown_ratio' => round($brownRatio, 2),
            'dark_ratio' => round($darkRatio, 2),
            'sample_size' => $sampleSize
        ]);

        return [
            'identified_type' => $condition,
            'confidence_score' => $confidence,
            'recommendations' => [
                'condition' => strtolower(str_replace(' ', '_', $condition)),
                'condition_label' => $condition,
                'recommendations' => $recommendations,
                'urgency_level' => $this->getUrgencyLevel($condition, $analysisType),
                'treatment_category' => $this->getTreatmentCategory($condition, $analysisType)
            ],
            'analysis_details' => [
                'analysis_method' => 'balanced_analysis',
                'green_ratio' => round($greenRatio, 2),
                'yellow_ratio' => round($yellowRatio, 2),
                'brown_ratio' => round($brownRatio, 2),
                'dark_ratio' => round($darkRatio, 2),
                'sample_size' => $sampleSize,
                'disease_detected' => ($brownRatio > 0.15 || $darkRatio > 0.08)
            ],
            'processing_time' => 0.5,
            'image_metadata' => [
                'width' => $imageInfo[0],
                'height' => $imageInfo[1],
                'mime_type' => $imageInfo['mime'],
                'file_size' => $photo->getSize()
            ]
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
                    '✅ Your leaves appear healthy - continue current excellent practices',
                    '💧 Maintain consistent watering: 1-1.5 inches per week, allowing soil to dry between waterings',
                    '🌱 Apply balanced fertilizer (10-10-10) every 4-6 weeks at rate of 1 pound per 100 square feet',
                    '☀️ Ensure 6-8 hours of direct sunlight daily - trim nearby plants if shading occurs',
                    '📸 For more accurate analysis, try uploading a clearer, well-lit image of individual leaves'
                ];
            case 'Yellowing Leaves':
                return [
                    '⚠️ Yellowing indicates overwatering or nitrogen deficiency - immediate action required',
                    '💧 Check soil moisture: insert finger 2 inches deep - if wet, reduce watering frequency',
                    '🌱 Apply nitrogen-rich fertilizer (21-0-0) at rate of 1/2 cup per plant',
                    '🔍 Test soil pH: optimal range is 6.0-7.0 - if below 6.0, add 1 cup lime per plant',
                    '📸 For more accurate analysis, try uploading a clearer, well-lit image of individual leaves'
                ];
            case 'Spotted/Diseased Leaves':
                return [
                    '🚨 Spots indicate fungal/bacterial infection - urgent treatment needed within 24 hours',
                    '🧪 Apply copper-based fungicide (copper sulfate) at rate of 2 tablespoons per gallon of water',
                    '🍃 Remove all spotted leaves immediately: cut at base, place in sealed bag',
                    '💧 Water only at soil level - avoid wetting leaves, use soaker hose or drip irrigation',
                    '📸 For more accurate analysis, try uploading a clearer, well-lit image of individual leaves'
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
                    '📸 For more accurate analysis, try uploading a clearer, well-lit image of the watermelon'
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
                    '📸 For more accurate analysis, try uploading a clearer, well-lit image of the watermelon'
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
}