<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    public function test_guest_is_redirected_to_login_when_visiting_dashboard(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function test_login_page_loads_for_guest(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Login - Kuitansi App', false);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->post('/login', [
            'nip' => 'nip-yang-tidak-ada',
            'password' => 'salah',
        ]);

        $response->assertSessionHasErrors('nip');
        $this->assertGuest();
    }
}
