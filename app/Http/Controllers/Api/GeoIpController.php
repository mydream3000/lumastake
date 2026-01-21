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
        $isLocalhost = ($ip === '127.0.0.1' || $ip === '::1');

        // For localhost, get real IP from external service and always use external API
        if ($isLocalhost) {
            $realIp = $this->getRealIpFromExternal();
            if ($realIp) {
                // On localhost, prefer external API for accuracy
                $countryCode = $this->getCountryFromExternalApi($realIp);
                if (!$countryCode) {
                    // Fallback to local GeoIP if external API fails
                    $countryCode = GeoIpHelper::getCountryCodeByIp($realIp);
                }
            } else {
                $countryCode = null;
            }
        } else {
            // For production: try local GeoIP first, then external API
            $countryCode = GeoIpHelper::getCountryCodeByIp($ip);
            if (!$countryCode) {
                $countryCode = $this->getCountryFromExternalApi($ip);
            }
        }

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
                'flag_class' => $country['flag_class'],
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

    /**
     * Get real IP from external service (for localhost development)
     */
    private function getRealIpFromExternal(): ?string
    {
        try {
            $response = @file_get_contents('https://api.ipify.org?format=json', false, stream_context_create([
                'http' => ['timeout' => 3]
            ]));
            if ($response) {
                $data = json_decode($response, true);
                return $data['ip'] ?? null;
            }
        } catch (\Exception $e) {
            // Silently fail
        }
        return null;
    }

    /**
     * Get country code from external API (fallback when local GeoIP fails)
     */
    private function getCountryFromExternalApi(?string $ip): ?string
    {
        if (!$ip) return null;

        try {
            $response = @file_get_contents("https://ipapi.co/{$ip}/country/", false, stream_context_create([
                'http' => ['timeout' => 3]
            ]));
            if ($response && strlen($response) === 2) {
                return strtoupper($response);
            }
        } catch (\Exception $e) {
            // Silently fail
        }
        return null;
    }
}
