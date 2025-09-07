<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Province;
use App\Models\Municipality;
use App\Models\Barangay;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Exception;

class ImportCompletePhilippineData extends Command
{
    protected $signature = 'locations:import-complete {--url=} {--file=}';
    protected $description = 'Import complete Philippine administrative data from GitHub repository';

    public function handle()
    {
        $this->info('🚀 Importing complete Philippine administrative data...');
        
        $url = $this->option('url') ?: 'https://raw.githubusercontent.com/flores-jacob/philippine-regions-provinces-cities-municipalities-barangays/master/philippine_provinces_cities_municipalities_and_barangays_2019v2.json';
        $filePath = $this->option('file') ?: storage_path('app/philippine_complete_2019v2.json');
        
        // Download the JSON file if not provided locally
        if (!$this->option('file')) {
            $this->info("📥 Downloading data from: {$url}");
            
            try {
                $response = Http::timeout(120)->get($url);
                if (!$response->ok()) {
                    throw new Exception("HTTP {$response->status()}: {$response->statusText()}");
                }
                
                file_put_contents($filePath, $response->body());
                $this->info('✅ Data downloaded successfully!');
            } catch (Exception $e) {
                $this->error("❌ Failed to download data: {$e->getMessage()}");
                $this->warn('💡 You can manually download the file and use --file=path/to/file.json');
                return self::FAILURE;
            }
        }
        
        // Load and parse the JSON data
        $this->info('📖 Loading JSON data...');
        
        try {
            $jsonData = json_decode(file_get_contents($filePath), true);
            if (!$jsonData) {
                throw new Exception('Invalid JSON data');
            }
            
            $this->info('✅ JSON data loaded successfully!');
        } catch (Exception $e) {
            $this->error("❌ Failed to parse JSON: {$e->getMessage()}");
            return self::FAILURE;
        }
        
        // Clear existing data
        $this->info('🧹 Clearing existing location data...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Barangay::truncate();
        Municipality::truncate();
        Province::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        $this->info('✅ Existing data cleared');
        
        // Import the data
        $this->info('📊 Importing Philippine administrative data...');
        
        $totalProvinces = 0;
        $totalMunicipalities = 0;
        $totalBarangays = 0;
        
        foreach ($jsonData as $regionCode => $regionData) {
            $this->info("🏛️ Processing Region: {$regionData['region_name']} ({$regionCode})");
            
            if (isset($regionData['province_list'])) {
                foreach ($regionData['province_list'] as $provinceName => $provinceData) {
                    // Create province with required fields
                    $province = Province::create([
                        'name' => $provinceName,
                        'code' => $regionCode . '_' . $totalProvinces, // Generate unique code
                        'region_code' => $regionCode,
                        'region_name' => $regionData['region_name']
                    ]);
                    $totalProvinces++;
                    
                    $this->line("  📍 Created Province: {$provinceName}");
                    
                    if (isset($provinceData['municipality_list'])) {
                        foreach ($provinceData['municipality_list'] as $municipalityName => $municipalityData) {
                            // Create municipality/city
                            $municipality = Municipality::create([
                                'province_id' => $province->id,
                                'name' => $municipalityName
                            ]);
                            $totalMunicipalities++;
                            
                            $this->line("    🏘️ Created Municipality/City: {$municipalityName}");
                            
                            if (isset($municipalityData['barangay_list']) && is_array($municipalityData['barangay_list'])) {
                                $barangayNames = $municipalityData['barangay_list'];
                                
                                // Create barangays
                                foreach ($barangayNames as $barangayName) {
                                    Barangay::create([
                                        'municipality_id' => $municipality->id,
                                        'name' => $barangayName
                                    ]);
                                    $totalBarangays++;
                                }
                                
                                $this->line("      🏠 Added " . count($barangayNames) . " barangays");
                            }
                        }
                    }
                }
            }
        }
        
        $this->info('✅ Import completed successfully!');
        $this->table(
            ['Metric', 'Count', 'Expected'],
            [
                ['Provinces', $totalProvinces, '81'],
                ['Municipalities/Cities', $totalMunicipalities, '1,634'],
                ['Barangays', number_format($totalBarangays), '42,046'],
                ['File Size', round(filesize($filePath) / 1024, 2) . ' KB', ''],
                ['Data Source', 'GitHub: flores-jacob/philippine-regions-provinces-cities-municipalities-barangays', ''],
                ['Data Year', '2019 (COMELEC Election Data)', '']
            ]
        );
        
        $this->newLine();
        $this->info('💡 Next steps:');
        $this->line('   1. Run: php artisan locations:generate-complete');
        $this->line('   2. Test the registration form');
        $this->line('   3. Verify all provinces have municipalities and barangays');
        
        return self::SUCCESS;
    }
}
