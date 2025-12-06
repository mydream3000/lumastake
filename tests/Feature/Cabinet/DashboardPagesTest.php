<?php

use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create([
        'email_verified_at' => now(),
    ]);
});

it('allows authenticated user to access dashboard', function () {
    $this->actingAs($this->user)
        ->get(route('cabinet.dashboard'))
        ->assertStatus(200);
});

it('allows authenticated user to access pools page', function () {
    $this->actingAs($this->user)
        ->get(route('cabinet.pools.index'))
        ->assertStatus(200);
});

it('allows authenticated user to access deposit page', function () {
    $this->actingAs($this->user)
        ->get(route('cabinet.deposit'))
        ->assertStatus(200);
});

it('allows authenticated user to access withdraw page', function () {
    $this->actingAs($this->user)
        ->get(route('cabinet.withdraw'))
        ->assertStatus(200);
});

it('allows authenticated user to access staking page', function () {
    $this->actingAs($this->user)
        ->get(route('cabinet.staking.index'))
        ->assertStatus(200);
});

it('redirects unauthenticated users from dashboard', function () {
    $this->get(route('cabinet.dashboard'))
        ->assertRedirect(route('login'));
});
