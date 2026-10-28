<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use Tests\TestCase;

class RegistrationTest extends TestCase
{
    public function test_public_registration_get_route_returns_404(): void
    {
        $this->get('/register')->assertNotFound();
    }

    public function test_public_registration_post_route_returns_404(): void
    {
        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertNotFound();
    }
}
