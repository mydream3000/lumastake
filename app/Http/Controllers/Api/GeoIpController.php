<?php

namespace App\Http\Controllers\Api;

use App\Helpers\GeoIpHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GeoIpController extends Controller
{
    /**
     * Get country by IP
     */
    public function getCountry(Request $request): JsonResponse
    {
        $ip = $request->ip();

        // For localhost testing, use a default IP
        if ($ip === '127.0.0.1' || $ip === '::1') {
            $ip = '8.8.8.8'; // Google DNS as fallback
        }

        $countryCode = GeoIpHelper::getCountryCodeByIp($ip);

        if (!$countryCode) {
            return response()->json([
                'success' => false,
                'country' => null,
            ]);
        }

        // Find full country info from our list
        $allCountries = GeoIpHelper::getAllCountries();
        $country = collect($allCountries)->firstWhere('code', $countryCode);

        if (!$country) {
            return response()->json([
                'success' => false,
                'country' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'country' => [
                'country_code' => $country['code'],
                'name' => $country['name'],
                'phone_code' => $country['phone_code'],
                'flag' => $country['flag'],
            ],
        ]);
    }

    /**
     * Get all countries
     */
    public function getAllCountries(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'countries' => GeoIpHelper::getAllCountries(),
        ]);
    }
}
