<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Province;
use App\Models\Municipality;

class FixNCRData extends Command
{
    protected $signature = 'locations:fix-ncr';
    protected $description = 'Fix NCR data structure by adding proper cities and municipality';

    public function handle()
    {
        $this->info('Fixing NCR data structure...');
        
        // Find NCR province
        $ncr = Province::where('name', 'like', '%NCR%')->first();
        
        if (!$ncr) {
            $this->error('NCR province not found!');
            return Command::FAILURE;
        }
        
        $this->info("Found NCR: {$ncr->name}");
        
        // NCR Cities and Municipality
        $ncrLocations = [
            'Manila',
            'Quezon City', 
            'Caloocan',
            'Las Piñas',
            'Makati',
            'Malabon',
            'Mandaluyong',
            'Marikina',
            'Muntinlupa',
            'Navotas',
            'Parañaque',
            'Pasay',
            'Pasig',
            'San Juan',
            'Taguig',
            'Valenzuela',
            'Pateros' // Municipality
        ];
        
        $created = 0;
        foreach ($ncrLocations as $locationName) {
            $municipality = Municipality::firstOrCreate([
                'province_id' => $ncr->id,
                'name' => $locationName
            ]);
            
            if ($municipality->wasRecentlyCreated) {
                $created++;
                $this->line("✅ Created: {$locationName}");
            } else {
                $this->line("ℹ️  Exists: {$locationName}");
            }
        }
        
        $this->info("✅ NCR data fixed! Created {$created} new locations.");
        $this->info("Total NCR locations: " . $ncr->municipalities()->count());
        
        return Command::SUCCESS;
    }
}
