<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Farm;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UsersSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{

		$users = [
			[
				'user' => ['name' => 'Maria Lopez', 'email' => 'maria.lopez@example.com', 'phone' => '+63 918 200 1001', 'password' => 'User@12345'],
				'farm' => [
					'farm_name' => 'Lopez Family Farm',
					'province_name' => 'Pampanga',
					'city_municipality_name' => 'Angeles City',
					'barangay_name' => 'Barangay Cutcut',
					'watermelon_variety' => 'Sugar Baby',
					'planting_date' => Carbon::now()->subDays(28),
					'field_size' => 1.50,
					'field_size_unit' => 'hectares',
				],
			],
			[
				'user' => ['name' => 'Ramon Perez', 'email' => 'ramon.perez@example.com', 'phone' => '+63 918 200 1002', 'password' => 'User@12345'],
				'farm' => [
					'farm_name' => 'Perez Agro Farm',
					'province_name' => 'Batangas',
					'city_municipality_name' => 'Batangas City',
					'barangay_name' => 'Barangay Alangilan',
					'watermelon_variety' => 'Crimson Sweet',
					'planting_date' => Carbon::now()->subDays(35),
					'field_size' => 0.80,
					'field_size_unit' => 'hectares',
				],
			],
			[
				'user' => ['name' => 'Lucia Ramos', 'email' => 'lucia.ramos@example.com', 'phone' => '+63 918 200 1003', 'password' => 'User@12345'],
				'farm' => [
					'farm_name' => 'Ramos Greenfields',
					'province_name' => 'Cebu',
					'city_municipality_name' => 'Cebu City',
					'barangay_name' => 'Barangay Lahug',
					'watermelon_variety' => 'Charleston Gray',
					'planting_date' => Carbon::now()->subDays(18),
					'field_size' => 1.10,
					'field_size_unit' => 'hectares',
				],
			],
			[
				'user' => ['name' => 'Pedro Gomez', 'email' => 'pedro.gomez@example.com', 'phone' => '+63 918 200 1004', 'password' => 'User@12345'],
				'farm' => [
					'farm_name' => 'Gomez Orchard',
					'province_name' => 'Davao',
					'city_municipality_name' => 'Davao City',
					'barangay_name' => 'Barangay Matina',
					'watermelon_variety' => 'Jubilee',
					'planting_date' => Carbon::now()->subDays(42),
					'field_size' => 2.20,
					'field_size_unit' => 'hectares',
				],
			],
			[
				'user' => ['name' => 'Sofia Cruz', 'email' => 'sofia.cruz@example.com', 'phone' => '+63 918 200 1005', 'password' => 'User@12345'],
				'farm' => [
					'farm_name' => 'Cruz Harvest Farm',
					'province_name' => 'Pangasinan',
					'city_municipality_name' => 'Dagupan',
					'barangay_name' => 'Barangay Bonuan',
					'watermelon_variety' => 'Allsweet',
					'planting_date' => Carbon::now()->subDays(24),
					'field_size' => 0.95,
					'field_size_unit' => 'hectares',
				],
			],
			[
				'user' => ['name' => 'Miguel Torres', 'email' => 'miguel.torres@example.com', 'phone' => '+63 918 200 1006', 'password' => 'User@12345'],
				'farm' => [
					'farm_name' => 'Torres Agri Ventures',
					'province_name' => 'Bukidnon',
					'city_municipality_name' => 'Malaybalay',
					'barangay_name' => 'Barangay Casisang',
					'watermelon_variety' => 'Crimson Sweet',
					'planting_date' => Carbon::now()->subDays(31),
					'field_size' => 1.75,
					'field_size_unit' => 'hectares',
				],
			],
			[
				'user' => ['name' => 'Ana Castillo', 'email' => 'ana.castillo@example.com', 'phone' => '+63 918 200 1007', 'password' => 'User@12345'],
				'farm' => [
					'farm_name' => 'Castillo Farms',
					'province_name' => 'Nueva Ecija',
					'city_municipality_name' => 'Cabanatuan',
					'barangay_name' => 'Barangay Aduas',
					'watermelon_variety' => 'Sugar Baby',
					'planting_date' => Carbon::now()->subDays(14),
					'field_size' => 0.65,
					'field_size_unit' => 'hectares',
				],
			],
			[
				'user' => ['name' => 'Diego Ramos', 'email' => 'diego.ramos@example.com', 'phone' => '+63 918 200 1008', 'password' => 'User@12345'],
				'farm' => [
					'farm_name' => 'Ramos Hillside Farm',
					'province_name' => 'Laguna',
					'city_municipality_name' => 'Calamba',
					'barangay_name' => 'Barangay Canlubang',
					'watermelon_variety' => 'Charleston Gray',
					'planting_date' => Carbon::now()->subDays(21),
					'field_size' => 1.30,
					'field_size_unit' => 'hectares',
				],
			],
			[
				'user' => ['name' => 'Elisa Fernandez', 'email' => 'elisa.fernandez@example.com', 'phone' => '+63 918 200 1009', 'password' => 'User@12345'],
				'farm' => [
					'farm_name' => 'Fernandez Valley Farm',
					'province_name' => 'Isabela',
					'city_municipality_name' => 'Ilagan',
					'barangay_name' => 'Barangay San Vicente',
					'watermelon_variety' => 'Jubilee',
					'planting_date' => Carbon::now()->subDays(39),
					'field_size' => 2.00,
					'field_size_unit' => 'hectares',
				],
			],
			[
				'user' => ['name' => 'Oscar Delgado', 'email' => 'oscar.delgado@example.com', 'phone' => '+63 918 200 1010', 'password' => 'User@12345'],
				'farm' => [
					'farm_name' => 'Delgado Family Estate',
					'province_name' => 'Tarlac',
					'city_municipality_name' => 'Tarlac City',
					'barangay_name' => 'Barangay San Isidro',
					'watermelon_variety' => 'Allsweet',
					'planting_date' => Carbon::now()->subDays(27),
					'field_size' => 1.20,
					'field_size_unit' => 'hectares',
				],
			],
		];

		foreach ($users as $entry) {
			$u = $entry['user'];
			$f = $entry['farm'];

			$user = User::updateOrCreate(
				['email' => $u['email']],
				[
					'name' => $u['name'],
					'role' => 'user',
					'phone' => $u['phone'],
					'email_verified_at' => now(),
					'password' => Hash::make($u['password']),
				]
			);

			Farm::updateOrCreate(
				[
					'user_id' => $user->id,
					'farm_name' => $f['farm_name'],
				],
				[
					'province_name' => $f['province_name'],
					'city_municipality_name' => $f['city_municipality_name'],
					'barangay_name' => $f['barangay_name'],
					'watermelon_variety' => $f['watermelon_variety'],
					'planting_date' => $f['planting_date'],
					'field_size' => $f['field_size'],
					'field_size_unit' => $f['field_size_unit'],
				]
			);
		}
	}
}


