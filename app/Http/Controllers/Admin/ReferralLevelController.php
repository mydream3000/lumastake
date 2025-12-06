<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReferralLevel;
use App\Models\User;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReferralLevelController extends Controller
{
    protected $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    /**
     * Display referral levels management page
     */
    public function index()
    {
        $levels = ReferralLevel::orderBy('level')->get();

        return view('admin.referral-levels.index', compact('levels'));
    }

    /**
     * Update referral level
     */
    public function update(Request $request, ReferralLevel $referralLevel)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'min_partners' => 'required|integer|min:0',
            'reward_percentage' => 'required|numeric|min:0|max:100',
        ]);

        try {
            DB::beginTransaction();

            $referralLevel->update([
                'name' => $request->name,
                'min_partners' => $request->min_partners,
                'reward_percentage' => $request->reward_percentage,
            ]);

            // Пересчитать уровни всех пользователей
            $this->recalculateAllUserLevels();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Referral level updated successfully. All user levels have been recalculated.',
                'level' => $referralLevel->fresh(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update referral level: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Recalculate referral levels for all users
     */
    protected function recalculateAllUserLevels()
    {
        // Получаем всех пользователей, у которых есть рефералы
        $usersWithReferrals = User::whereHas('referrals')->get();

        foreach ($usersWithReferrals as $user) {
            $this->referralService->recalculateReferralLevel($user);
        }
    }
}
