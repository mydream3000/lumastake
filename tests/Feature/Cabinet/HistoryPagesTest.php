<?php

use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create([
        'email_verified_at' => now(),
    ]);
});

it('allows authenticated user to access history page', function () {
    $this->actingAs($this->user)
        ->get(route('cabinet.history'))
        ->assertStatus(200);
});

it('allows authenticated user to access transactions deposits page', function () {
    $this->actingAs($this->user)
        ->get(route('cabinet.transactions.deposits'))
        ->assertStatus(200);
});

it('allows authenticated user to access transactions withdraw page', function () {
    $this->actingAs($this->user)
        ->get(route('cabinet.transactions.withdraw'))
        ->assertStatus(200);
});

it('redirects unauthenticated users from history', function () {
    $this->get(route('cabinet.history'))
        ->assertRedirect(route('login'));
});

it('redirects unauthenticated users from transactions deposits', function () {
    $this->get(route('cabinet.transactions.deposits'))
        ->assertRedirect(route('login'));
});

it('redirects unauthenticated users from transactions withdraw', function () {
    $this->get(route('cabinet.transactions.withdraw'))
        ->assertRedirect(route('login'));
});
