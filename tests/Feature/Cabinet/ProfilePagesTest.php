<?php

use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create([
        'email_verified_at' => now(),
    ]);
});

it('allows authenticated user to access profile page', function () {
    $this->actingAs($this->user)
        ->get(route('cabinet.profile.show'))
        ->assertStatus(200);
});

it('allows authenticated user to access profile edit page', function () {
    $this->actingAs($this->user)
        ->get(route('cabinet.profile.edit'))
        ->assertStatus(200);
});

it('allows authenticated user to access referrals page', function () {
    $this->actingAs($this->user)
        ->get(route('cabinet.referrals'))
        ->assertStatus(200);
});

it('allows authenticated user to access profit earnings page', function () {
    $this->actingAs($this->user)
        ->get(route('cabinet.earnings.profit'))
        ->assertStatus(200);
});

it('allows authenticated user to access rewards earnings page', function () {
    $this->actingAs($this->user)
        ->get(route('cabinet.earnings.rewards'))
        ->assertStatus(200);
});

it('allows authenticated user to access rewards page', function () {
    $this->actingAs($this->user)
        ->get(route('cabinet.rewards'))
        ->assertStatus(200);
});

it('redirects unauthenticated users from profile', function () {
    $this->get(route('cabinet.profile.show'))
        ->assertRedirect(route('login'));
});

it('redirects unauthenticated users from referrals', function () {
    $this->get(route('cabinet.referrals'))
        ->assertRedirect(route('login'));
});

it('redirects unauthenticated users from rewards page', function () {
    $this->get(route('cabinet.rewards'))
        ->assertRedirect(route('login'));
});
