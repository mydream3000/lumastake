<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CaptureReferralParam
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Capture referral code only for guests
        if (!$request->user()) {
            $ref = (string) $request->query('ref', '');
            if ($ref !== '') {
                $refTrim = trim($ref);
                $refUpper = Str::upper($refTrim);

                $refUser = null;
                if (Str::isUuid($refTrim)) {
                    $refUser = User::where('uuid', $refTrim)->first();
                } else {
                    $refUser = User::where('referral_code', $refUpper)->first();
                }

                if ($refUser) {
                    // Store referrer user id (primary), keep raw ref for debugging
                    $request->session()->put('referrer_user_id', $refUser->id);
                    $request->session()->put('ref', $refTrim);

                    Log::info('Referral captured into session', [
                        'ref' => $refTrim,
                        'referrer_user_id' => $refUser->id,
                    ]);
                } else {
                    Log::warning('Referral not found, skipping capture', ['ref' => $refTrim]);
                }
            }
        }

        return $next($request);
    }
}
