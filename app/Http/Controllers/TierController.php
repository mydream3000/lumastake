<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use App\Models\Tier;

class TierController extends BaseController
{
    public function index()
    {
        $tiers = Tier::query()->with(['percentages' => function ($q) { $q->orderBy('days'); }])->orderBy('level')->get();
        return view('public.profit-tiers', compact('tiers'));
    }
}
