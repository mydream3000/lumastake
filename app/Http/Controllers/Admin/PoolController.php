<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TierPercentage;
use App\Models\TierPercentageIslamic;
use App\Models\Tier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PoolController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $query = TierPercentage::with('tier:id,name,level')
                ->join('tiers', 'tier_percentages.tier_id', '=', 'tiers.id')
                ->select('tier_percentages.*')
                ->orderBy('tier_percentages.days')
                ->orderBy('tiers.level');

            // Filter by duration
            if ($request->filled('duration')) {
                $query->where('tier_percentages.days', $request->duration);
            }

            // Search: по tier.name или по days
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('tier_percentages.days', 'like', "%{$search}%")
                      ->orWhere('tiers.name', 'like', "%{$search}%");
                });
            }

            $pools = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'data' => $pools->items(),
                'total' => $pools->total(),
                'per_page' => $pools->perPage(),
                'current_page' => $pools->currentPage(),
                'last_page' => $pools->lastPage(),
            ]);
        }

        $durations = [10, 30, 60, 90, 180];

        return view('admin.pools.index', compact('durations'));
    }

    public function editPercentages(Request $request)
    {
        $days = $request->get('days', 10);
        $accountType = $request->get('type', 'normal');

        if ($accountType === 'islamic') {
            $pools = TierPercentageIslamic::with('tier:id,name,level,min_balance,max_balance')
                ->where('duration_days', $days)
                ->join('tiers', 'tier_percentages_islamic.tier_id', '=', 'tiers.id')
                ->orderBy('tiers.level')
                ->select('tier_percentages_islamic.*')
                ->get();
        } else {
            $pools = TierPercentage::with('tier:id,name,level,min_balance,max_balance')
                ->where('days', $days)
                ->join('tiers', 'tier_percentages.tier_id', '=', 'tiers.id')
                ->orderBy('tiers.level')
                ->select('tier_percentages.*')
                ->get();
        }

        $durations = [10, 30, 60, 90, 180];

        return view('admin.pools.percentages', compact('pools', 'days', 'durations', 'accountType'));
    }

    public function updatePercentages(Request $request)
    {
        $accountType = $request->get('type', 'normal');

        if ($accountType === 'islamic') {
            $request->validate([
                'pools' => 'required|array',
                'pools.*.id' => 'required|exists:tier_percentages_islamic,id',
                'pools.*.min_percentage' => 'required|string',
                'pools.*.max_percentage' => 'required|string',
            ]);

            try {
                DB::beginTransaction();

                foreach ($request->pools as $poolData) {
                    TierPercentageIslamic::where('id', $poolData['id'])
                        ->update([
                            'min_percentage' => $poolData['min_percentage'],
                            'max_percentage' => $poolData['max_percentage'],
                        ]);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Islamic percentages updated successfully',
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update Islamic percentages: ' . $e->getMessage(),
                ], 500);
            }
        } else {
            $request->validate([
                'pools' => 'required|array',
                'pools.*.id' => 'required|exists:tier_percentages,id',
                'pools.*.percentage' => 'required|numeric|min:0|max:200',
            ]);

            try {
                DB::beginTransaction();

                foreach ($request->pools as $poolData) {
                    TierPercentage::where('id', $poolData['id'])
                        ->update(['percentage' => $poolData['percentage']]);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Percentages updated successfully',
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update percentages: ' . $e->getMessage(),
                ], 500);
            }
        }
    }
}
