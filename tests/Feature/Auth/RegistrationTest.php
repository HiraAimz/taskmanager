<?php

declare(strict_types=1);

test('public registration GET route returns 404', function () {
    $this->get('/register')->assertNotFound();
});

test('public registration POST route returns 404', function () {
    $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertNotFound();
});
