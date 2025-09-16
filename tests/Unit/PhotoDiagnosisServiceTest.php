<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Diagnosis\PhotoDiagnosisService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PhotoDiagnosisServiceTest extends TestCase
{
    private PhotoDiagnosisService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PhotoDiagnosisService();
        Storage::fake('public');
    }

    /**
     * Test that the service can handle basic image analysis
     */
    public function test_can_analyze_image()
    {
        // Create a simple test image
        $image = imagecreate(100, 100);
        $green = imagecolorallocate($image, 0, 255, 0);
        imagefill($image, 0, 0, $green);
        
        $tempPath = tempnam(sys_get_temp_dir(), 'test_image') . '.jpg';
        imagejpeg($image, $tempPath);
        imagedestroy($image);

        $file = new UploadedFile(
            $tempPath,
            'test.jpg',
            'image/jpeg',
            null,
            true
        );

        $result = $this->service->analyze($file, 'leaves');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('identified_type', $result);
        $this->assertArrayHasKey('confidence_score', $result);
        $this->assertArrayHasKey('recommendations', $result);
        $this->assertArrayHasKey('analysis_details', $result);
        
        // Clean up
        unlink($tempPath);
    }

    /**
     * Test that the service provides detailed analysis information
     */
    public function test_provides_detailed_analysis()
    {
        // Create a test image with mixed colors
        $image = imagecreate(200, 200);
        $green = imagecolorallocate($image, 0, 255, 0);
        $brown = imagecolorallocate($image, 139, 69, 19);
        
        // Fill with green
        imagefill($image, 0, 0, $green);
        // Add some brown spots
        imagefilledrectangle($image, 50, 50, 70, 70, $brown);
        imagefilledrectangle($image, 120, 120, 140, 140, $brown);
        
        $tempPath = tempnam(sys_get_temp_dir(), 'test_image') . '.jpg';
        imagejpeg($image, $tempPath);
        imagedestroy($image);

        $file = new UploadedFile(
            $tempPath,
            'test.jpg',
            'image/jpeg',
            null,
            true
        );

        $result = $this->service->analyze($file, 'leaves');

        $this->assertArrayHasKey('analysis_details', $result);
        $this->assertArrayHasKey('color_analysis', $result['analysis_details']);
        $this->assertArrayHasKey('texture_analysis', $result['analysis_details']);
        $this->assertArrayHasKey('pattern_analysis', $result['analysis_details']);
        $this->assertArrayHasKey('image_quality', $result['analysis_details']);
        
        // Clean up
        unlink($tempPath);
    }

    /**
     * Test that confidence scores are reasonable
     */
    public function test_confidence_scores_are_reasonable()
    {
        $image = imagecreate(100, 100);
        $green = imagecolorallocate($image, 0, 255, 0);
        imagefill($image, 0, 0, $green);
        
        $tempPath = tempnam(sys_get_temp_dir(), 'test_image') . '.jpg';
        imagejpeg($image, $tempPath);
        imagedestroy($image);

        $file = new UploadedFile(
            $tempPath,
            'test.jpg',
            'image/jpeg',
            null,
            true
        );

        $result = $this->service->analyze($file, 'leaves');

        $this->assertIsInt($result['confidence_score']);
        $this->assertGreaterThanOrEqual(30, $result['confidence_score']);
        $this->assertLessThanOrEqual(95, $result['confidence_score']);
        
        // Clean up
        unlink($tempPath);
    }

    /**
     * Test that the service handles different analysis types
     */
    public function test_handles_different_analysis_types()
    {
        $image = imagecreate(100, 100);
        $green = imagecolorallocate($image, 0, 255, 0);
        imagefill($image, 0, 0, $green);
        
        $tempPath = tempnam(sys_get_temp_dir(), 'test_image') . '.jpg';
        imagejpeg($image, $tempPath);
        imagedestroy($image);

        $file = new UploadedFile(
            $tempPath,
            'test.jpg',
            'image/jpeg',
            null,
            true
        );

        // Test leaves analysis
        $leavesResult = $this->service->analyze($file, 'leaves');
        $this->assertIsArray($leavesResult);
        $this->assertArrayHasKey('identified_type', $leavesResult);

        // Test watermelon analysis
        $watermelonResult = $this->service->analyze($file, 'watermelon');
        $this->assertIsArray($watermelonResult);
        $this->assertArrayHasKey('identified_type', $watermelonResult);
        
        // Clean up
        unlink($tempPath);
    }

    /**
     * Test that the service provides enhanced recommendations
     */
    public function test_provides_enhanced_recommendations()
    {
        $image = imagecreate(100, 100);
        $green = imagecolorallocate($image, 0, 255, 0);
        imagefill($image, 0, 0, $green);
        
        $tempPath = tempnam(sys_get_temp_dir(), 'test_image') . '.jpg';
        imagejpeg($image, $tempPath);
        imagedestroy($image);

        $file = new UploadedFile(
            $tempPath,
            'test.jpg',
            'image/jpeg',
            null,
            true
        );

        $result = $this->service->analyze($file, 'leaves');

        $this->assertArrayHasKey('recommendations', $result);
        $this->assertArrayHasKey('recommendations', $result['recommendations']);
        $this->assertIsArray($result['recommendations']['recommendations']);
        $this->assertGreaterThan(0, count($result['recommendations']['recommendations']));
        
        // Clean up
        unlink($tempPath);
    }
}
