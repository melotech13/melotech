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
				'user' => [
					'name' => 'Juan Dela Cruz',
					'email' => 'juan.delacruz@example.com',
					'phone' => '+639171234567',
					'password' => 'password123'
				],
				'farm' => [
					'farm_name' => 'Dela Cruz Watermelon Farm',
					'province_name' => 'Laguna',
					'city_municipality_name' => 'Calamba',
					'barangay_name' => 'Pansol',
					'watermelon_variety' => 'Sugar Baby',
					'planting_date' => '2024-01-15',
					'land_size' => 2.5,
					'land_size_unit' => 'ha'
				]
			],
			[
				'user' => [
					'name' => 'Maria Santos',
					'email' => 'maria.santos@example.com',
					'phone' => '+639171234568',
					'password' => 'password123'
				],
				'farm' => [
					'farm_name' => 'Santos Family Farm',
					'province_name' => 'Batangas',
					'city_municipality_name' => 'Tanauan',
					'barangay_name' => 'Altura Matanda',
					'watermelon_variety' => 'Crimson Sweet',
					'planting_date' => '2024-02-01',
					'land_size' => 1.8,
					'land_size_unit' => 'ha'
				]
			],
			[
				'user' => [
					'name' => 'Pedro Garcia',
					'email' => 'pedro.garcia@example.com',
					'phone' => '+639171234569',
					'password' => 'password123'
				],
				'farm' => [
					'farm_name' => 'Garcia Organic Farm',
					'province_name' => 'Quezon',
					'city_municipality_name' => 'Lucena',
					'barangay_name' => 'Iyam',
					'watermelon_variety' => 'Charleston Gray',
					'planting_date' => '2024-01-20',
					'land_size' => 3.2,
					'land_size_unit' => 'ha'
				]
			],
			[
				'user' => [
					'name' => 'Ana Rodriguez',
					'email' => 'ana.rodriguez@example.com',
					'phone' => '+639171234570',
					'password' => 'password123'
				],
				'farm' => [
					'farm_name' => 'Rodriguez Watermelon Fields',
					'province_name' => 'Cavite',
					'city_municipality_name' => 'Silang',
					'barangay_name' => 'Pulong Bunga',
					'watermelon_variety' => 'Sugar Baby',
					'planting_date' => '2024-02-10',
					'land_size' => 2.0,
					'land_size_unit' => 'ha'
				]
			],
			[
				'user' => [
					'name' => 'Carlos Mendoza',
					'email' => 'carlos.mendoza@example.com',
					'phone' => '+639171234571',
					'password' => 'password123'
				],
				'farm' => [
					'farm_name' => 'Mendoza Farmstead',
					'province_name' => 'Rizal',
					'city_municipality_name' => 'Antipolo',
					'barangay_name' => 'Cupang',
					'watermelon_variety' => 'Crimson Sweet',
					'planting_date' => '2024-01-25',
					'land_size' => 1.5,
					'land_size_unit' => 'ha'
				]
			],
			[
				'user' => [
					'name' => 'Elena Torres',
					'email' => 'elena.torres@example.com',
					'phone' => '+639171234572',
					'password' => 'password123'
				],
				'farm' => [
					'farm_name' => 'Torres Watermelon Garden',
					'province_name' => 'Laguna',
					'city_municipality_name' => 'San Pablo',
					'barangay_name' => 'San Rafael',
					'watermelon_variety' => 'Charleston Gray',
					'planting_date' => '2024-02-05',
					'land_size' => 2.8,
					'land_size_unit' => 'ha'
				]
			],
			[
				'user' => [
					'name' => 'Roberto Cruz',
					'email' => 'roberto.cruz@example.com',
					'phone' => '+639171234573',
					'password' => 'password123'
				],
				'farm' => [
					'farm_name' => 'Cruz Family Farm',
					'province_name' => 'Batangas',
					'city_municipality_name' => 'Lipa',
					'barangay_name' => 'Mataas na Lupa',
					'watermelon_variety' => 'Sugar Baby',
					'planting_date' => '2024-01-30',
					'land_size' => 1.2,
					'land_size_unit' => 'ha'
				]
			],
			[
				'user' => [
					'name' => 'Isabel Ramos',
					'email' => 'isabel.ramos@example.com',
					'phone' => '+639171234574',
					'password' => 'password123'
				],
				'farm' => [
					'farm_name' => 'Ramos Watermelon Plantation',
					'province_name' => 'Quezon',
					'city_municipality_name' => 'Tayabas',
					'barangay_name' => 'Ibas',
					'watermelon_variety' => 'Crimson Sweet',
					'planting_date' => '2024-02-15',
					'land_size' => 3.5,
					'land_size_unit' => 'ha'
				]
			],
			[
				'user' => [
					'name' => 'Miguel Lopez',
					'email' => 'miguel.lopez@example.com',
					'phone' => '+639171234575',
					'password' => 'password123'
				],
				'farm' => [
					'farm_name' => 'Lopez Organic Watermelons',
					'province_name' => 'Cavite',
					'city_municipality_name' => 'Trece Martires',
					'barangay_name' => 'Osorio',
					'watermelon_variety' => 'Charleston Gray',
					'planting_date' => '2024-01-18',
					'land_size' => 2.3,
					'land_size_unit' => 'ha'
				]
			],
			[
				'user' => [
					'name' => 'Carmen Villanueva',
					'email' => 'carmen.villanueva@example.com',
					'phone' => '+639171234576',
					'password' => 'password123'
				],
				'farm' => [
					'farm_name' => 'Villanueva Farm',
					'province_name' => 'Rizal',
					'city_municipality_name' => 'Taytay',
					'barangay_name' => 'San Juan',
					'watermelon_variety' => 'Sugar Baby',
					'planting_date' => '2024-02-20',
					'land_size' => 1.8,
					'land_size_unit' => 'ha'
				]
			],
			[
				'user' => [
					'name' => 'Fernando Reyes',
					'email' => 'fernando.reyes@example.com',
					'phone' => '+639171234577',
					'password' => 'password123'
				],
				'farm' => [
					'farm_name' => 'Reyes Watermelon Fields',
					'province_name' => 'Laguna',
					'city_municipality_name' => 'Los Baños',
					'barangay_name' => 'Putho',
					'watermelon_variety' => 'Crimson Sweet',
					'planting_date' => '2024-01-12',
					'land_size' => 2.7,
					'land_size_unit' => 'ha'
				]
			],
			[
				'user' => [
					'name' => 'Patricia Morales',
					'email' => 'patricia.morales@example.com',
					'phone' => '+639171234578',
					'password' => 'password123'
				],
				'farm' => [
					'farm_name' => 'Morales Family Farm',
					'province_name' => 'Batangas',
					'city_municipality_name' => 'Bauan',
					'barangay_name' => 'San Andres',
					'watermelon_variety' => 'Charleston Gray',
					'planting_date' => '2024-02-08',
					'land_size' => 1.9,
					'land_size_unit' => 'ha'
				]
			],
			[
				'user' => [
					'name' => 'Antonio Gutierrez',
					'email' => 'antonio.gutierrez@example.com',
					'phone' => '+639171234579',
					'password' => 'password123'
				],
				'farm' => [
					'farm_name' => 'Gutierrez Watermelon Garden',
					'province_name' => 'Quezon',
					'city_municipality_name' => 'Lucban',
					'barangay_name' => 'Ibabang Dupay',
					'watermelon_variety' => 'Sugar Baby',
					'planting_date' => '2024-01-22',
					'land_size' => 2.1,
					'land_size_unit' => 'ha'
				]
			],
			[
				'user' => [
					'name' => 'Rosa Herrera',
					'email' => 'rosa.herrera@example.com',
					'phone' => '+639171234580',
					'password' => 'password123'
				],
				'farm' => [
					'farm_name' => 'Herrera Organic Farm',
					'province_name' => 'Cavite',
					'city_municipality_name' => 'General Trias',
					'barangay_name' => 'San Francisco',
					'watermelon_variety' => 'Crimson Sweet',
					'planting_date' => '2024-02-12',
					'land_size' => 3.0,
					'land_size_unit' => 'ha'
				]
			],
			[
				'user' => [
					'name' => 'Jose Jimenez',
					'email' => 'jose.jimenez@example.com',
					'phone' => '+639171234581',
					'password' => 'password123'
				],
				'farm' => [
					'farm_name' => 'Jimenez Watermelon Plantation',
					'province_name' => 'Rizal',
					'city_municipality_name' => 'Cainta',
					'barangay_name' => 'San Juan',
					'watermelon_variety' => 'Charleston Gray',
					'planting_date' => '2024-01-28',
					'land_size' => 1.6,
					'land_size_unit' => 'ha'
				]
			],
			[
				'user' => [
					'name' => 'Sofia Castillo',
					'email' => 'sofia.castillo@example.com',
					'phone' => '+639171234582',
					'password' => 'password123'
				],
				'farm' => [
					'farm_name' => 'Castillo Farmstead',
					'province_name' => 'Laguna',
					'city_municipality_name' => 'Santa Cruz',
					'barangay_name' => 'Pagsawitan',
					'watermelon_variety' => 'Sugar Baby',
					'planting_date' => '2024-02-18',
					'land_size' => 2.4,
					'land_size_unit' => 'ha'
				]
			],
			[
				'user' => [
					'name' => 'Ramon Aguilar',
					'email' => 'ramon.aguilar@example.com',
					'phone' => '+639171234583',
					'password' => 'password123'
				],
				'farm' => [
					'farm_name' => 'Aguilar Watermelon Fields',
					'province_name' => 'Batangas',
					'city_municipality_name' => 'Santo Tomas',
					'barangay_name' => 'San Agustin',
					'watermelon_variety' => 'Crimson Sweet',
					'planting_date' => '2024-01-14',
					'land_size' => 1.7,
					'land_size_unit' => 'ha'
				]
			],
			[
				'user' => [
					'name' => 'Teresa Moreno',
					'email' => 'teresa.moreno@example.com',
					'phone' => '+639171234584',
					'password' => 'password123'
				],
				'farm' => [
					'farm_name' => 'Moreno Family Farm',
					'province_name' => 'Quezon',
					'city_municipality_name' => 'Sariaya',
					'barangay_name' => 'Castañas',
					'watermelon_variety' => 'Charleston Gray',
					'planting_date' => '2024-02-03',
					'land_size' => 2.9,
					'land_size_unit' => 'ha'
				]
			],
			[
				'user' => [
					'name' => 'Luis Vargas',
					'email' => 'luis.vargas@example.com',
					'phone' => '+639171234585',
					'password' => 'password123'
				],
				'farm' => [
					'farm_name' => 'Vargas Watermelon Garden',
					'province_name' => 'Cavite',
					'city_municipality_name' => 'Imus',
					'barangay_name' => 'Bucandala',
					'watermelon_variety' => 'Sugar Baby',
					'planting_date' => '2024-01-26',
					'land_size' => 2.2,
					'land_size_unit' => 'ha'
				]
			],
			[
				'user' => [
					'name' => 'Elena Flores',
					'email' => 'elena.flores@example.com',
					'phone' => '+639171234586',
					'password' => 'password123'
				],
				'farm' => [
					'farm_name' => 'Flores Organic Watermelons',
					'province_name' => 'Rizal',
					'city_municipality_name' => 'Rodriguez',
					'barangay_name' => 'San Jose',
					'watermelon_variety' => 'Crimson Sweet',
					'planting_date' => '2024-02-14',
					'land_size' => 1.4,
					'land_size_unit' => 'ha'
				]
			],
			[
				'user' => [
					'name' => 'Francisco Ruiz',
					'email' => 'francisco.ruiz@example.com',
					'phone' => '+639171234587',
					'password' => 'password123'
				],
				'farm' => [
					'farm_name' => 'Ruiz Watermelon Plantation',
					'province_name' => 'Laguna',
					'city_municipality_name' => 'Biñan',
					'barangay_name' => 'Malamig',
					'watermelon_variety' => 'Charleston Gray',
					'planting_date' => '2024-01-19',
					'land_size' => 3.1,
					'land_size_unit' => 'ha'
				]
			],
			[
				'user' => [
					'name' => 'Monica Silva',
					'email' => 'monica.silva@example.com',
					'phone' => '+639171234588',
					'password' => 'password123'
				],
				'farm' => [
					'farm_name' => 'Silva Farm',
					'province_name' => 'Batangas',
					'city_municipality_name' => 'Calaca',
					'barangay_name' => 'Lalayat',
					'watermelon_variety' => 'Sugar Baby',
					'planting_date' => '2024-02-07',
					'land_size' => 2.6,
					'land_size_unit' => 'ha'
				]
			],
			[
				'user' => [
					'name' => 'Alberto Medina',
					'email' => 'alberto.medina@example.com',
					'phone' => '+639171234589',
					'password' => 'password123'
				],
				'farm' => [
					'farm_name' => 'Medina Watermelon Fields',
					'province_name' => 'Quezon',
					'city_municipality_name' => 'Candelaria',
					'barangay_name' => 'Malabanban Norte',
					'watermelon_variety' => 'Crimson Sweet',
					'planting_date' => '2024-01-24',
					'land_size' => 1.9,
					'land_size_unit' => 'ha'
				]
			],
			[
				'user' => [
					'name' => 'Beatriz Ortega',
					'email' => 'beatriz.ortega@example.com',
					'phone' => '+639171234590',
					'password' => 'password123'
				],
				'farm' => [
					'farm_name' => 'Ortega Family Farm',
					'province_name' => 'Cavite',
					'city_municipality_name' => 'Kawit',
					'barangay_name' => 'Magdalo',
					'watermelon_variety' => 'Charleston Gray',
					'planting_date' => '2024-02-11',
					'land_size' => 2.8,
					'land_size_unit' => 'ha'
				]
			],
			[
				'user' => [
					'name' => 'Ricardo Navarro',
					'email' => 'ricardo.navarro@example.com',
					'phone' => '+639171234591',
					'password' => 'password123'
				],
				'farm' => [
					'farm_name' => 'Navarro Watermelon Garden',
					'province_name' => 'Rizal',
					'city_municipality_name' => 'San Mateo',
					'barangay_name' => 'Ampid',
					'watermelon_variety' => 'Sugar Baby',
					'planting_date' => '2024-01-17',
					'land_size' => 1.3,
					'land_size_unit' => 'ha'
				]
			],
			[
				'user' => [
					'name' => 'Adriana Pena',
					'email' => 'adriana.pena@example.com',
					'phone' => '+639171234592',
					'password' => 'password123'
				],
				'farm' => [
					'farm_name' => 'Pena Organic Farm',
					'province_name' => 'Laguna',
					'city_municipality_name' => 'Alaminos',
					'barangay_name' => 'San Gregorio',
					'watermelon_variety' => 'Crimson Sweet',
					'planting_date' => '2024-02-16',
					'land_size' => 3.3,
					'land_size_unit' => 'ha'
				]
			],
			[
				'user' => [
					'name' => 'Hector Delgado',
					'email' => 'hector.delgado@example.com',
					'phone' => '+639171234593',
					'password' => 'password123'
				],
				'farm' => [
					'farm_name' => 'Delgado Watermelon Plantation',
					'province_name' => 'Batangas',
					'city_municipality_name' => 'Nasugbu',
					'barangay_name' => 'Aguado',
					'watermelon_variety' => 'Charleston Gray',
					'planting_date' => '2024-01-21',
					'land_size' => 2.5,
					'land_size_unit' => 'ha'
				]
			],
			[
				'user' => [
					'name' => 'Gloria Rios',
					'email' => 'gloria.rios@example.com',
					'phone' => '+639171234594',
					'password' => 'password123'
				],
				'farm' => [
					'farm_name' => 'Rios Farmstead',
					'province_name' => 'Quezon',
					'city_municipality_name' => 'Tiaong',
					'barangay_name' => 'Lalig',
					'watermelon_variety' => 'Sugar Baby',
					'planting_date' => '2024-02-09',
					'land_size' => 1.8,
					'land_size_unit' => 'ha'
				]
			],
			[
				'user' => [
					'name' => 'Vicente Mendez',
					'email' => 'vicente.mendez@example.com',
					'phone' => '+639171234595',
					'password' => 'password123'
				],
				'farm' => [
					'farm_name' => 'Mendez Watermelon Fields',
					'province_name' => 'Cavite',
					'city_municipality_name' => 'Bacoor',
					'barangay_name' => 'Molino',
					'watermelon_variety' => 'Crimson Sweet',
					'planting_date' => '2024-01-23',
					'land_size' => 2.7,
					'land_size_unit' => 'ha'
				]
			],
			[
				'user' => [
					'name' => 'Leticia Vega',
					'email' => 'leticia.vega@example.com',
					'phone' => '+639171234596',
					'password' => 'password123'
				],
				'farm' => [
					'farm_name' => 'Vega Family Farm',
					'province_name' => 'Rizal',
					'city_municipality_name' => 'Angono',
					'barangay_name' => 'Bagong Barrio',
					'watermelon_variety' => 'Charleston Gray',
					'planting_date' => '2024-02-13',
					'land_size' => 1.6,
					'land_size_unit' => 'ha'
				]
			]
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
					'password' => $u['password'], // Store as plain text like admin accounts
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
					'land_size' => $f['land_size'],
					'land_size_unit' => $f['land_size_unit'],
				]
			);
		}
	}
}

