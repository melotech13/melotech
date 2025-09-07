<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Province;
use App\Models\Municipality;
use App\Models\Barangay;
use Illuminate\Support\Facades\Storage;

class GenerateCompleteLocationsJson extends Command
{
    protected $signature = 'locations:generate-complete {--minify : Minify the JSON output}';
    protected $description = 'Generate a complete locations JSON with enhanced Philippine administrative data';

    public function handle()
    {
        $this->info('Generating complete Philippine locations JSON...');
        
        $startTime = microtime(true);
        
        // Build the nested location structure
        $locations = [];
        
        $provinces = Province::with(['municipalities.barangays'])
            ->orderBy('name')
            ->get();
            
        $this->info("Processing {$provinces->count()} provinces...");
        
        $totalMunicipalities = 0;
        $totalBarangays = 0;
        $municipalitiesWithBarangays = 0;
        
        foreach ($provinces as $province) {
            $provinceName = $province->name;
            $locations[$provinceName] = [];
            
            foreach ($province->municipalities->sortBy('name') as $municipality) {
                $municipalityName = $municipality->name;
                $barangays = $municipality->barangays->sortBy('name')->pluck('name')->toArray();
                
                $locations[$provinceName][$municipalityName] = $barangays;
                
                $totalBarangays += count($barangays);
                $totalMunicipalities++;
                
                if (count($barangays) > 0) {
                    $municipalitiesWithBarangays++;
                }
            }
        }
        
        // Generate the JSON
        $jsonFlags = JSON_UNESCAPED_UNICODE;
        if (!$this->option('minify')) {
            $jsonFlags |= JSON_PRETTY_PRINT;
        }
        
        $json = json_encode($locations, $jsonFlags);
        
        // Save to public directory
        $filename = 'locations.json';
        $path = public_path($filename);
        file_put_contents($path, $json);
        
        // Also save a backup in storage
        Storage::disk('local')->put('locations/' . $filename, $json);
        
        $endTime = microtime(true);
        $executionTime = round(($endTime - $startTime) * 1000, 2);
        
        $fileSize = round(filesize($path) / 1024, 2);
        
        $this->info('✅ Complete locations JSON generated successfully!');
        $this->table(
            ['Metric', 'Value', 'Expected'],
            [
                ['File Path', $path, ''],
                ['File Size', $fileSize . ' KB', ''],
                ['Provinces', $provinces->count(), '81'],
                ['Total Municipalities/Cities', $totalMunicipalities, '1,634 (148 cities + 1,486 municipalities)'],
                ['Municipalities with Barangays', $municipalitiesWithBarangays, '~1,600+'],
                ['Total Barangays', number_format($totalBarangays), '42,046'],
                ['Generation Time', $executionTime . ' ms', ''],
                ['Minified', $this->option('minify') ? 'Yes' : 'No', '']
            ]
        );
        
        $this->newLine();
        $this->warn('⚠️  Data Quality Issues:');
        $this->line('   - Expected 81 provinces, got ' . $provinces->count());
        $this->line('   - Expected 42,046 barangays, got ' . number_format($totalBarangays));
        $this->line('   - Only ' . $municipalitiesWithBarangays . ' out of ' . $totalMunicipalities . ' municipalities have barangays');
        
        $this->newLine();
        $this->info('💡 Recommendations:');
        $this->line('   1. Import complete PSGC data from official sources');
        $this->line('   2. Use alternative data sources (GitHub repositories, APIs)');
        $this->line('   3. Manually populate missing barangay data');
        
        return self::SUCCESS;
    }
}
