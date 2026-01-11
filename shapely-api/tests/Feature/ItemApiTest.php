<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User; 
use Laravel\Sanctum\Sanctum; 

class ItemApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_add_items()
    {
        // 1. Try to add an item WITHOUT logging in
        $response = $this->postJson('/api/items', [
            'name' => 'Hacker Item',
            'shape' => 'Circle',
            'color' => '#000000',
        ]);

        // 2. Expect a 401 Unauthorized error
        $response->assertStatus(401);
    }

    /** @test */
    public function logged_in_admin_can_add_items()
    {
        // 1. Create a fake user (Admin)
        $user = User::factory()->create();

        // 2. "Act As" that user (Log them in for this test)
        Sanctum::actingAs($user);

        // 3. Define data
        $data = [
            'name' => 'Valid Item',
            'shape' => 'Square',
            'color' => '#ff0000',
        ];

        // 4. Send POST request
        $response = $this->postJson('/api/items', $data);

        // 5. Expect 201 Created
        $response->assertStatus(201);
        $this->assertDatabaseHas('items', $data);
    }

    /** @test */
    public function logged_in_admin_can_delete_items()
    {
        // 1. Create user and item
        $user = User::factory()->create();
        $item = Item::create([
            'name' => 'Delete Me', 
            'shape' => 'Triangle', 
            'color' => '#00ff00'
        ]);

        // 2. Act As User
        Sanctum::actingAs($user);

        // 3. Send DELETE request
        $response = $this->deleteJson("/api/items/{$item->id}");

        // 4. Expect 204 (Success)
        $response->assertStatus(204);
        $this->assertDatabaseMissing('items', ['id' => $item->id]);
    }
}