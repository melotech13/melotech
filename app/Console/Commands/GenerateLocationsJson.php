<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Province;
use App\Models\Municipality;
use App\Models\Barangay;
use Illuminate\Support\Facades\Storage;

class GenerateLocationsJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'locations:generate-json {--minify : Minify the JSON output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a preloaded JSON file containing all location data (provinces, municipalities, barangays)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating preloaded locations JSON...');
        
        $startTime = microtime(true);
        
        // Build the nested location structure
        $locations = [];
        
        $provinces = Province::with(['municipalities.barangays'])
            ->orderBy('name')
            ->get();
            
        $this->info("Processing {$provinces->count()} provinces...");
        
        $totalMunicipalities = 0;
        $totalBarangays = 0;
        
        foreach ($provinces as $province) {
            $provinceName = $province->name;
            $locations[$provinceName] = [];
            
            foreach ($province->municipalities->sortBy('name') as $municipality) {
                $municipalityName = $municipality->name;
                $locations[$provinceName][$municipalityName] = [];
                
                $barangays = $municipality->barangays->sortBy('name')->pluck('name')->toArray();
                $locations[$provinceName][$municipalityName] = $barangays;
                
                $totalBarangays += count($barangays);
                $totalMunicipalities++;
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
        
        $this->info('✅ Locations JSON generated successfully!');
        $this->table(
            ['Metric', 'Value'],
            [
                ['File Path', $path],
                ['File Size', $fileSize . ' KB'],
                ['Provinces', $provinces->count()],
                ['Municipalities/Cities', $totalMunicipalities],
                ['Barangays', number_format($totalBarangays)],
                ['Generation Time', $executionTime . ' ms'],
                ['Minified', $this->option('minify') ? 'Yes' : 'No']
            ]
        );
        
        $this->newLine();
        $this->info('💡 Usage in frontend:');
        $this->line('   - Access via: /locations.json');
        $this->line('   - Load once and handle cascading in browser');
        $this->line('   - No AJAX calls needed after initial load');
        
        return self::SUCCESS;
    }
}
