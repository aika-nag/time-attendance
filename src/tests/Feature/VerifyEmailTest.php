<?php

namespace Tests\Feature;

use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use Tests\TestCase;

class VerifyEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_email_send()
    {
        Notification::fake();
        Notification::assertNothingSent();

        $response = $this->post('/register', [
            'name' => 'テストユーザ',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'test@example.com')->first();
        Notification::assertSentTo(
            [$user], VerifyEmailNotification::class
        );
        Notification::assertTimesSent(
            1, VerifyEmailNotification::class
        );
    }

    public function test_redirect_verify_page()
    {
        Notification::fake();
        Notification::assertNothingSent();

        $user = User::create([
            'name' => 'テストユーザ',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->assertNull($user->email_verified_at);
        $response = $this->actingAs($user)->get('/');

        $response->assertRedirect('/email/verify');
        $response = $this->get('http://localhost:8025');
        $response->assertStatus(200);
    }
}
