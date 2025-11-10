<?php

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Hash;

it('logs in and returns token', function () {
    $user = User::create([
        'name' => 'Demo',
        'email' => 'demo@example.com',
        'password' => Hash::make('secret'),
    ]);

    $response = $this->postJson('/auth/login', [
        'email' => 'demo@example.com',
        'password' => 'secret',
    ]);

    $response->assertOk()
        ->assertJsonStructure(['token', 'user']);
});
