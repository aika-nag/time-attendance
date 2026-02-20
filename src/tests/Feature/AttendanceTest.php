<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Dflydev\DotAccessData\Data;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_example()
    {
        $user = User::find(1);
        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);
    }
}
