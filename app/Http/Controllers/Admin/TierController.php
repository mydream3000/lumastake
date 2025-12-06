<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tier;
use Illuminate\Http\Request;

class TierController extends Controller
{
    /**
     * Display a listing of tiers
     */
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            $tiers = Tier::orderBy('level')->get();

            return response()->json([
                'data' => $tiers->map(function ($tier) {
                    return [
                        'id' => $tier->id,
                        'level' => $tier->level,
                        'name' => $tier->name,
                        'min_balance' => $tier->min_balance,
                        'max_balance' => $tier->max_balance,
                        'created_at' => $tier->created_at->format('d, M, Y'),
                    ];
                }),
                'total' => $tiers->count(),
                'per_page' => $tiers->count(),
                'current_page' => 1,
                'last_page' => 1,
            ]);
        }

        return view('admin.tiers.index');
    }

    /**
     * Show edit form
     */
    public function edit(Request $request, Tier $tier)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'id' => $tier->id,
                'level' => $tier->level,
                'name' => $tier->name,
                'min_balance' => $tier->min_balance,
                'max_balance' => $tier->max_balance,
            ]);
        }

        $tier->load('tierPercentages');
        return view('admin.tiers.edit', compact('tier'));
    }

    /**
     * Update tier
     */
    public function update(Request $request, Tier $tier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'min_balance' => 'required|numeric|min:0',
            // Разрешаем равенство для границ диапазона, max может быть null (безлимит)
            'max_balance' => 'nullable|numeric|min:0|gte:min_balance',
        ]);

        $tier->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tier updated successfully',
            ]);
        }

        return redirect()->route('admin.tiers.index')
            ->with('success', 'Tier updated successfully');
    }

    /**
     * Update tier percentages (APR by duration)
     */
    public function updatePercentages(Request $request, Tier $tier)
    {
        $validated = $request->validate([
            'percentages' => 'required|array',
            'percentages.*.duration' => 'required|integer|min:1',
            'percentages.*.percentage' => 'required|numeric|min:0|max:100',
        ]);

        foreach ($validated['percentages'] as $data) {
            $tier->tierPercentages()->updateOrCreate(
                ['duration' => $data['duration']],
                ['percentage' => $data['percentage']]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Tier percentages updated successfully',
        ]);
    }
}
