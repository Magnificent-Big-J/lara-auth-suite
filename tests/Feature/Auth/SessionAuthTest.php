<?php

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Hash;

it('logs in with session and accesses /auth/session/me', function () {
    $u = User::create([
        'name' => 'Sessy',
        'email' => 'sessy@example.com',
        'password' => Hash::make('secret'),
    ]);

    // Hit login
    $res = $this->post('/auth/session/login', [
        'email' => 'sessy@example.com',
        'password' => 'secret',
    ])->assertOk();

    // Cookie-based session now in the test client; can access protected route
    $this->get('/auth/session/me')
        ->assertOk()
        ->assertJsonPath('email', 'sessy@example.com');

    // Logout
    $this->post('/auth/session/logout')->assertOk();
    $this->get('/auth/session/me')->assertUnauthorized();
});
