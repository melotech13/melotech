<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Farm;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WeatherTest extends TestCase
{
    use RefreshDatabase;

    public function test_weather_routes_require_authentication()
    {
        $response = $this->get('/weather/user-farm');
        $response->assertRedirect('/login');
    }

    public function test_user_can_access_weather_for_their_farm()
    {
        $user = User::factory()->create();
        $farm = Farm::factory()->create([
            'user_id' => $user->id,
            'farm_name' => 'Test Farm',
            'city_municipality_name' => 'Manila',
            'province_name' => 'Metro Manila'
        ]);

        $response = $this->actingAs($user)
                        ->get("/weather/farm/{$farm->id}");

        $response->assertStatus(200);
    }

    public function test_user_cannot_access_weather_for_other_users_farm()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $farm = Farm::factory()->create([
            'user_id' => $user2->id,
            'farm_name' => 'Other User Farm',
            'city_municipality_name' => 'Manila',
            'province_name' => 'Metro Manila'
        ]);

        $response = $this->actingAs($user1)
                        ->get("/weather/farm/{$farm->id}");

        $response->assertStatus(404);
    }

    public function test_weather_refresh_requires_authentication()
    {
        $response = $this->post('/weather/refresh/1');
        $response->assertRedirect('/login');
    }
}
