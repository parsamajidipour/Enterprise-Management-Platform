<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Regression test for the CVE-2026-48019 mitigation (see App\Rules\NoCrlf
 * and SECURITY.md). Laravel's built-in `email` rule alone does not reject
 * embedded CR/LF, which would otherwise open header/Bcc injection in
 * anything that later treats this field as an email header.
 */
class LoginCrlfInjectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_containing_crlf_is_rejected(): void
    {
        User::factory()->create([
            'email'     => 'victim@example.com',
            'password'  => Hash::make('password'),
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email'    => "victim@example.com\r\nBcc: attacker@evil.com",
            'password' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_normal_email_still_logs_in(): void
    {
        User::factory()->create([
            'email'     => 'victim@example.com',
            'password'  => Hash::make('password'),
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email'    => 'victim@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.email', 'victim@example.com')
            ->assertJsonStructure(['data' => ['token']]);
    }
}
