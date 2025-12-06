<?php

namespace App\Providers;

use App\Models\ReferralLevel;
use App\Models\StakingDeposit;
use App\Models\User;
use App\Observers\ReferralLevelObserver;
use App\Observers\StakingDepositObserver;
use App\Observers\UserObserver;
use App\View\Composers\FooterComposer;
use App\View\Composers\SeoComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        StakingDeposit::observe(StakingDepositObserver::class);
        ReferralLevel::observe(ReferralLevelObserver::class);

        View::composer(['layouts._partials.footer', 'layouts._partials.header'], FooterComposer::class);
        View::composer('layouts.public', SeoComposer::class);
    }
}
