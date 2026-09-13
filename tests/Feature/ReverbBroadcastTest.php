<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Service;

class ReverbBroadcastTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_request_broadcasts_to_reverb()
    {
        Event::fake();

        $customer = User::factory()->create();
        $category = Category::create(['name' => 'Test', 'description' => 'Test', 'icon' => 'test', 'is_active' => true]);
        $service = Service::create(['category_id' => $category->id, 'name' => 'Fix', 'base_price' => 10, 'is_fixed_price' => true, 'description' => 'd', 'is_active' => true]);

        $response = $this->actingAs($customer)->postJson('/api/service-requests', [
            'service_id' => $service->id,
            'description' => 'Help me',
            'latitude' => 24.0,
            'longitude' => 46.0,
            'address' => 'Riyadh',
        ]);

        $response->assertStatus(201);

        Event::assertDispatched(\App\Events\NewServiceRequestEvent::class, function ($event) use ($category) {
            $channels = $event->broadcastOn();
            $this->assertEquals('private-providers.category.' . $category->id, $channels[0]->name);
            return true;
        });
    }
}
