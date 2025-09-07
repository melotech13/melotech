<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Province;
use App\Models\Municipality;
use App\Models\Barangay;

class AddNCRBarangays extends Command
{
    protected $signature = 'locations:add-ncr-barangays';
    protected $description = 'Add sample barangays for NCR cities';

    public function handle()
    {
        $this->info('Adding barangays for NCR cities...');
        
        // Find NCR province
        $ncr = Province::where('name', 'like', '%NCR%')->first();
        
        if (!$ncr) {
            $this->error('NCR province not found!');
            return self::FAILURE;
        }
        
        // Sample barangays for major NCR cities
        $ncrBarangays = [
            'Manila' => [
                'Barangay 1', 'Barangay 2', 'Barangay 3', 'Barangay 4', 'Barangay 5',
                'Barangay 6', 'Barangay 7', 'Barangay 8', 'Barangay 9', 'Barangay 10'
            ],
            'Quezon City' => [
                'Alicia', 'Bagong Silangan', 'Batasan Hills', 'Commonwealth', 'Culiat',
                'E. Rodriguez', 'Holy Spirit', 'Immaculate Concepcion', 'Kamuning', 'Katipunan'
            ],
            'Makati' => [
                'Bangkal', 'Bel-Air', 'Carmona', 'Cembo', 'Comembo',
                'Dasmariñas', 'East Rembo', 'Forbes Park', 'Guadalupe Nuevo', 'Guadalupe Viejo'
            ],
            'Taguig' => [
                'Bagumbayan', 'Bambang', 'Calzada', 'Hagonoy', 'Ibayo-Tipas',
                'Ligid-Tipas', 'Lower Bicutan', 'Maharlika Village', 'Napindan', 'New Lower Bicutan'
            ]
        ];
        
        $totalBarangays = 0;
        
        foreach ($ncrBarangays as $cityName => $barangays) {
            $city = Municipality::where('province_id', $ncr->id)
                               ->where('name', $cityName)
                               ->first();
            
            if ($city) {
                $this->info("Adding barangays for {$cityName}...");
                
                foreach ($barangays as $barangayName) {
                    $barangay = Barangay::firstOrCreate([
                        'municipality_id' => $city->id,
                        'name' => $barangayName
                    ]);
                    
                    if ($barangay->wasRecentlyCreated) {
                        $totalBarangays++;
                        $this->line("  ✅ Created: {$barangayName}");
                    }
                }
            }
        }
        
        $this->info("✅ Added {$totalBarangays} barangays for NCR cities!");
        
        return self::SUCCESS;
    }
}
