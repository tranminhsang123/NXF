<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthGoogleLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_callback_handles_token_connection_failure_gracefully(): void
    {
        Http::fake([
            'https://oauth2.googleapis.com/token' => function () {
                throw new ConnectionException('Could not connect to Google.');
            },
        ]);

        $this->withSession(['google_oauth_state' => 'valid-state'])
            ->get(route('login.google.callback', [
                'state' => 'valid-state',
                'code' => 'fake-code',
            ]))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');
    }

    public function test_google_api_login_handles_tokeninfo_connection_failure_gracefully(): void
    {
        Http::fake([
            'https://oauth2.googleapis.com/tokeninfo*' => function () {
                throw new ConnectionException('Could not connect to Google.');
            },
        ]);

        $this->postJson('/api/auth/google', [
            'id_token' => 'fake-id-token',
            'device_name' => 'test-device',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('id_token');
    }
}
