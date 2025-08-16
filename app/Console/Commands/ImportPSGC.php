<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use App\Models\Province;
use App\Models\Municipality;
use App\Models\Barangay;

class ImportPSGC extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:psgc {--path=} {--url=} {--json} {--api}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import PSGC data into provinces, municipalities, and barangays tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->option('path') ?: storage_path('app/psgc.xlsx');
        $url = $this->option('url') ?: 'https://psa.gov.ph/sites/default/files/2024PSGC.xlsx';

        if (!is_file($filePath)) {
            $this->info("Downloading PSGC data...");
            try {
                $response = Http::withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36',
                        'Accept' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/octet-stream,*/*'
                    ])
                    ->withOptions([
                        'allow_redirects' => [
                            'max' => 15,
                            'strict' => false,
                            'referer' => true,
                            'track_redirects' => true,
                        ],
                        'verify' => false,
                        'timeout' => 120,
                    ])
                    ->get($url);

                if (!$response->ok()) {
                    $this->error('Failed to download PSGC file. HTTP ' . $response->status());
                    return self::FAILURE;
                }

                file_put_contents($filePath, $response->body());
            } catch (\Throwable $e) {
                $this->error('Failed to download PSGC file: ' . $e->getMessage());
                $this->warn('Tip: Manually download the file and re-run with --path=storage/app/psgc.xlsx');
                return self::FAILURE;
            }
        } else {
            $this->info("Using existing file: " . $filePath);
        }

        if ($this->option('json')) {
            return $this->importFromJson();
        }
        if ($this->option('api')) {
            return $this->importFromApi();
        }

        $this->info("Reading Excel file...");
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $this->info("Inserting into database...");
        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // skip header

            [$regionCode, $provinceCode, $municipalityCode, $barangayCode, $name, $level] = array_pad($row, 6, null);

            if ($level === 'Province') {
                Province::firstOrCreate(['name' => $name]);
            } elseif ($level === 'City' || $level === 'Municipality') {
                // NOTE: This matching assumes the sheet includes the actual province name or mappable code
                $province = Province::where('name', 'like', "%$provinceCode%")->first();
                if ($province) {
                    Municipality::firstOrCreate([
                        'province_id' => $province->id,
                        'name' => $name,
                    ]);
                }
            } elseif ($level === 'Barangay') {
                $municipality = Municipality::where('name', 'like', "%$municipalityCode%")->first();
                if ($municipality) {
                    Barangay::firstOrCreate([
                        'municipality_id' => $municipality->id,
                        'name' => $name,
                    ]);
                }
            }
        }

        $this->info("✅ PSGC Import Complete!");
        return self::SUCCESS;
    }

    private function importFromJson(): int
    {
        $this->info('Importing from JSON sources...');
        try {
            $provinces = Http::retry(2, 200)->timeout(60)->get('https://raw.githubusercontent.com/psgc-dev/psgc-data/main/provinces.json')->json();
            $citiesMunicipalities = Http::retry(2, 200)->timeout(60)->get('https://raw.githubusercontent.com/psgc-dev/psgc-data/main/cities-municipalities.json')->json();
            $barangays = Http::retry(2, 200)->timeout(60)->get('https://raw.githubusercontent.com/psgc-dev/psgc-data/main/barangays.json')->json();
        } catch (\Throwable $e) {
            $this->error('Failed to fetch JSON: ' . $e->getMessage());
            return self::FAILURE;
        }

        if (!is_array($provinces) || !is_array($citiesMunicipalities) || !is_array($barangays)) {
            $this->error('JSON response invalid.');
            return self::FAILURE;
        }

        $codeToProvinceId = [];
        foreach ($provinces as $p) {
            $prov = Province::firstOrCreate(
                ['code' => $p['code'] ?? ($p['psgcCode'] ?? null)],
                ['name' => $p['name'] ?? ($p['fullName'] ?? 'Unknown')]
            );
            if ($prov && $prov->code) {
                $codeToProvinceId[$prov->code] = $prov->id;
            }
        }

        $codeToMunicipalityId = [];
        foreach ($citiesMunicipalities as $c) {
            $code = $c['code'] ?? ($c['psgcCode'] ?? null);
            $name = $c['name'] ?? ($c['fullName'] ?? null);
            $provinceCode = $c['provinceCode'] ?? ($c['province_code'] ?? null);
            if (!$code || !$name || !$provinceCode) continue;
            $provinceId = $codeToProvinceId[$provinceCode] ?? null;
            if (!$provinceId) continue;
            $mun = Municipality::firstOrCreate(
                ['code' => $code],
                ['province_id' => $provinceId, 'name' => $name]
            );
            $codeToMunicipalityId[$code] = $mun->id;
        }

        foreach ($barangays as $b) {
            $code = $b['code'] ?? ($b['psgcCode'] ?? null);
            $name = $b['name'] ?? ($b['fullName'] ?? null);
            $cityCode = $b['cityCode'] ?? ($b['city_code'] ?? null);
            $municipalityCode = $b['municipalityCode'] ?? ($b['municipality_code'] ?? null);
            $parentCode = $cityCode ?: $municipalityCode;
            if (!$code || !$name || !$parentCode) continue;
            $municipalityId = $codeToMunicipalityId[$parentCode] ?? null;
            if (!$municipalityId) continue;
            Barangay::firstOrCreate(
                ['code' => $code],
                ['municipality_id' => $municipalityId, 'name' => $name]
            );
        }

        $this->info('✅ JSON import complete');
        return self::SUCCESS;
    }

    private function importFromApi(): int
    {
        $this->info('Importing from PSGC API...');

        $fetch = function (string $url) {
            $urls = [$url, rtrim($url, '/') . '.json'];
            foreach ($urls as $u) {
                try {
                    $resp = Http::retry(2, 200)
                        ->timeout(60)
                        ->acceptJson()
                        ->withoutVerifying()
                        ->get($u);
                    if ($resp->ok() && is_array($resp->json())) {
                        return $resp->json();
                    }
                } catch (\Throwable $e) {
                    // continue
                }
            }
            return [];
        };

        // Provinces
        $provinces = $fetch('https://psgc.gitlab.io/api/provinces/');
        $codeToProvinceId = [];
        foreach ($provinces as $p) {
            $code = $p['code'] ?? ($p['psgcCode'] ?? null);
            $name = $p['name'] ?? ($p['fullName'] ?? null);
            if (!$name) continue;
            $unique = $code ? ['code' => $code] : ['name' => $name];
            $values = ['name' => $name];
            if (Schema::hasColumn('provinces', 'region_code')) {
                $values['region_code'] = $p['regionCode'] ?? '000000000';
            }
            if (Schema::hasColumn('provinces', 'region_name')) {
                $values['region_name'] = $p['regionName'] ?? '';
            }
            $prov = Province::firstOrCreate($unique, $values);
            if ($prov && $code) {
                $codeToProvinceId[$code] = $prov->id;
            }
        }

        // Cities/Municipalities per province
        $codeToMunicipalityId = [];
        foreach (array_keys($codeToProvinceId) as $provCode) {
            $cmList = $fetch("https://psgc.gitlab.io/api/provinces/{$provCode}/cities-municipalities/");
            $provinceId = $codeToProvinceId[$provCode];
            foreach ($cmList as $c) {
                $code = $c['code'] ?? ($c['psgcCode'] ?? null);
                $name = $c['name'] ?? ($c['fullName'] ?? null);
                if (!$name) continue;
                $mun = Municipality::firstOrCreate(
                    ['code' => $code],
                    ['province_id' => $provinceId, 'name' => $name]
                );
                if ($code) {
                    $codeToMunicipalityId[$code] = $mun->id;
                }
            }
        }

        // Barangays per city/municipality
        foreach (array_keys($codeToMunicipalityId) as $munCode) {
            $bList = $fetch("https://psgc.gitlab.io/api/cities-municipalities/{$munCode}/barangays/");
            $municipalityId = $codeToMunicipalityId[$munCode];
            foreach ($bList as $b) {
                $code = $b['code'] ?? ($b['psgcCode'] ?? null);
                $name = $b['name'] ?? ($b['fullName'] ?? null);
                if (!$name) continue;
                Barangay::firstOrCreate(
                    ['code' => $code],
                    ['municipality_id' => $municipalityId, 'name' => $name]
                );
            }
        }

        $this->info('✅ API import complete');
        return self::SUCCESS;
    }
}
