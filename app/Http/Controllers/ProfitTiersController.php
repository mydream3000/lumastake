<?php

namespace App\Http\Controllers;

use App\Models\Tier;
use App\Models\InvestmentPool;
use Illuminate\Http\Request;

class ProfitTiersController extends Controller
{
    public function index(Request $request)
    {
        $accountType = $request->query('type') === 'islamic' ? 'islamic' : 'normal';

        if ($accountType === 'islamic') {
            $tiers = Tier::with(['islamicPercentages' => function ($query) {
                $query->orderBy('duration_days', 'asc');
            }])->orderBy('level', 'asc')->get();
        } else {
            $tiers = Tier::with(['percentages' => function ($query) {
                $query->orderBy('days', 'asc');
            }])->orderBy('level', 'asc')->get();
        }

        $seoKey = 'tiers';

        return view('public.profit-tiers', compact('tiers', 'seoKey', 'accountType'));
    }
}
