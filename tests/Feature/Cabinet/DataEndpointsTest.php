<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->user = User::factory()->create([
        'email_verified_at' => now(),
    ]);
});

describe('Pools Data Endpoint', function () {
    it('returns pools data for authenticated user', function () {
        $this->actingAs($this->user)
            ->get(route('cabinet.pools.data'))
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'days',
                        'min_stake',
                        'profit',
                    ]
                ]
            ]);
    });

    it('redirects unauthenticated users from pools data', function () {
        $this->get(route('cabinet.pools.data'))
            ->assertRedirect(route('login'));
    });
});

describe('Staking Data Endpoint', function () {
    it('returns staking data for authenticated user', function () {
        $this->actingAs($this->user)
            ->get(route('cabinet.staking.data'))
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'
            ]);
    });

    it('redirects unauthenticated users from staking data', function () {
        $this->get(route('cabinet.staking.data'))
            ->assertRedirect(route('login'));
    });
});

describe('History Data Endpoint', function () {
    it('returns history data for authenticated user', function () {
        $this->actingAs($this->user)
            ->get(route('cabinet.history.data'))
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'
            ]);
    });

    it('redirects unauthenticated users from history data', function () {
        $this->get(route('cabinet.history.data'))
            ->assertRedirect(route('login'));
    });
});

describe('Transactions Deposits Data Endpoint', function () {
    it('returns deposits data for authenticated user', function () {
        $this->actingAs($this->user)
            ->get(route('cabinet.transactions.deposits.data'))
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'
            ]);
    });

    it('redirects unauthenticated users from deposits data', function () {
        $this->get(route('cabinet.transactions.deposits.data'))
            ->assertRedirect(route('login'));
    });
});

describe('Transactions Withdraw Data Endpoint', function () {
    it('returns withdraw data for authenticated user', function () {
        $this->actingAs($this->user)
            ->get(route('cabinet.transactions.withdraw.data'))
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'
            ]);
    });

    it('redirects unauthenticated users from withdraw data', function () {
        $this->get(route('cabinet.transactions.withdraw.data'))
            ->assertRedirect(route('login'));
    });
});

describe('Referrals Data Endpoint', function () {
    it('returns referrals data for authenticated user', function () {
        $this->actingAs($this->user)
            ->get(route('cabinet.referrals.data'))
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'
            ]);
    });

    it('redirects unauthenticated users from referrals data', function () {
        $this->get(route('cabinet.referrals.data'))
            ->assertRedirect(route('login'));
    });
});

describe('Earnings Profit Data Endpoint', function () {
    it('returns profit earnings data for authenticated user', function () {
        $this->actingAs($this->user)
            ->get(route('cabinet.earnings.profit.data'))
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'
            ]);
    });

    it('redirects unauthenticated users from profit data', function () {
        $this->get(route('cabinet.earnings.profit.data'))
            ->assertRedirect(route('login'));
    });
});

describe('Earnings Rewards Data Endpoint', function () {
    it('returns rewards earnings data for authenticated user', function () {
        $this->actingAs($this->user)
            ->get(route('cabinet.earnings.rewards.data'))
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'
            ]);
    });

    it('redirects unauthenticated users from rewards data', function () {
        $this->get(route('cabinet.earnings.rewards.data'))
            ->assertRedirect(route('login'));
    });
});
