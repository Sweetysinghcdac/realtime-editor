<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
class DocumentTest extends TestCase
{
     use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_user_can_create_document() {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $resp = $this->postJson('/api/documents', ['title'=>'Hello','content'=>'world']);
        $resp->assertStatus(201)->assertJsonFragment(['title'=>'Hello']);
        $this->assertDatabaseHas('documents',['title'=>'Hello','owner_id'=>$user->id]);
    }
}
