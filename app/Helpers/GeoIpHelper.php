<?php

namespace App\Helpers;

use GeoIp2\Database\Reader;
use Illuminate\Support\Facades\Log;

class GeoIpHelper
{
    /**
     * Get country ISO code by IP address
     *
     * @param string $ip
     * @return string|null
     */
    public static function getCountryCodeByIp(string $ip): ?string
    {
        try {
            // Path to GeoLite2 database
            $dbPath = storage_path('app/geoip/GeoLite2-City.mmdb');

            if (!file_exists($dbPath)) {
                return null;
            }

            // Ignore private and reserved IP ranges
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                return null;
            }

            $reader = new Reader($dbPath);
            $record = $reader->city($ip);

            return $record->country->isoCode ?? null;

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get country name by IP address
     *
     * @param string $ip
     * @return string|null
     */
    public static function getCountryByIp(string $ip): ?string
    {
        try {
            // Path to GeoLite2 database
            $dbPath = storage_path('app/geoip/GeoLite2-City.mmdb');

            if (!file_exists($dbPath)) {
                Log::warning('GeoLite2 database not found at: ' . $dbPath);
                return null;
            }

            // Ignore private and reserved IP ranges
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                return null;
            }

            $reader = new Reader($dbPath);
            $record = $reader->city($ip);

            return $record->country->name ?? null;

        } catch (\GeoIp2\Exception\AddressNotFoundException $e) {
            // This is a common case for local/private IPs, so we don't log it as an error.
            return null;
        } catch (\Exception $e) {
            Log::error('GeoIP lookup failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get phone code by country ISO code
     */
    public static function getPhoneCodeByCountry(string $countryCode): string
    {
        $phoneCodes = [
            'US' => '+1', 'CA' => '+1', 'GB' => '+44', 'AU' => '+61', 'DE' => '+49',
            'FR' => '+33', 'ES' => '+34', 'IT' => '+39', 'NL' => '+31', 'BE' => '+32',
            'CH' => '+41', 'AT' => '+43', 'SE' => '+46', 'NO' => '+47', 'DK' => '+45',
            'FI' => '+358', 'PL' => '+48', 'RU' => '+7', 'UA' => '+380', 'BY' => '+375',
            'KZ' => '+7', 'TR' => '+90', 'SA' => '+966', 'AE' => '+971', 'IL' => '+972',
            'EG' => '+20', 'ZA' => '+27', 'NG' => '+234', 'KE' => '+254', 'IN' => '+91',
            'CN' => '+86', 'JP' => '+81', 'KR' => '+82', 'TH' => '+66', 'VN' => '+84',
            'SG' => '+65', 'MY' => '+60', 'ID' => '+62', 'PH' => '+63', 'BR' => '+55',
            'MX' => '+52', 'AR' => '+54', 'CL' => '+56', 'CO' => '+57', 'PE' => '+51',
            'VE' => '+58', 'NZ' => '+64', 'IE' => '+353', 'PT' => '+351', 'GR' => '+30',
            'CZ' => '+420', 'HU' => '+36', 'RO' => '+40', 'BG' => '+359', 'HR' => '+385',
            'RS' => '+381', 'SK' => '+421', 'SI' => '+386', 'LT' => '+370', 'LV' => '+371',
            'EE' => '+372', 'IS' => '+354', 'LU' => '+352', 'MT' => '+356', 'CY' => '+357',
        ];

        return $phoneCodes[$countryCode] ?? '+1';
    }

    /**
     * Get all countries with phone codes (full list)
     */
    public static function getAllCountries(): array
    {
        return [
            // A
            ['code' => 'AF', 'name' => 'Afghanistan', 'phone_code' => '+93', 'flag' => '🇦🇫'],
            ['code' => 'AL', 'name' => 'Albania', 'phone_code' => '+355', 'flag' => '🇦🇱'],
            ['code' => 'DZ', 'name' => 'Algeria', 'phone_code' => '+213', 'flag' => '🇩🇿'],
            ['code' => 'AD', 'name' => 'Andorra', 'phone_code' => '+376', 'flag' => '🇦🇩'],
            ['code' => 'AO', 'name' => 'Angola', 'phone_code' => '+244', 'flag' => '🇦🇴'],
            ['code' => 'AG', 'name' => 'Antigua and Barbuda', 'phone_code' => '+1268', 'flag' => '🇦🇬'],
            ['code' => 'AR', 'name' => 'Argentina', 'phone_code' => '+54', 'flag' => '🇦🇷'],
            ['code' => 'AM', 'name' => 'Armenia', 'phone_code' => '+374', 'flag' => '🇦🇲'],
            ['code' => 'AU', 'name' => 'Australia', 'phone_code' => '+61', 'flag' => '🇦🇺'],
            ['code' => 'AT', 'name' => 'Austria', 'phone_code' => '+43', 'flag' => '🇦🇹'],
            ['code' => 'AZ', 'name' => 'Azerbaijan', 'phone_code' => '+994', 'flag' => '🇦🇿'],
            // B
            ['code' => 'BS', 'name' => 'Bahamas', 'phone_code' => '+1242', 'flag' => '🇧🇸'],
            ['code' => 'BH', 'name' => 'Bahrain', 'phone_code' => '+973', 'flag' => '🇧🇭'],
            ['code' => 'BD', 'name' => 'Bangladesh', 'phone_code' => '+880', 'flag' => '🇧🇩'],
            ['code' => 'BB', 'name' => 'Barbados', 'phone_code' => '+1246', 'flag' => '🇧🇧'],
            ['code' => 'BY', 'name' => 'Belarus', 'phone_code' => '+375', 'flag' => '🇧🇾'],
            ['code' => 'BE', 'name' => 'Belgium', 'phone_code' => '+32', 'flag' => '🇧🇪'],
            ['code' => 'BZ', 'name' => 'Belize', 'phone_code' => '+501', 'flag' => '🇧🇿'],
            ['code' => 'BJ', 'name' => 'Benin', 'phone_code' => '+229', 'flag' => '🇧🇯'],
            ['code' => 'BT', 'name' => 'Bhutan', 'phone_code' => '+975', 'flag' => '🇧🇹'],
            ['code' => 'BO', 'name' => 'Bolivia', 'phone_code' => '+591', 'flag' => '🇧🇴'],
            ['code' => 'BA', 'name' => 'Bosnia and Herzegovina', 'phone_code' => '+387', 'flag' => '🇧🇦'],
            ['code' => 'BW', 'name' => 'Botswana', 'phone_code' => '+267', 'flag' => '🇧🇼'],
            ['code' => 'BR', 'name' => 'Brazil', 'phone_code' => '+55', 'flag' => '🇧🇷'],
            ['code' => 'BN', 'name' => 'Brunei', 'phone_code' => '+673', 'flag' => '🇧🇳'],
            ['code' => 'BG', 'name' => 'Bulgaria', 'phone_code' => '+359', 'flag' => '🇧🇬'],
            ['code' => 'BF', 'name' => 'Burkina Faso', 'phone_code' => '+226', 'flag' => '🇧🇫'],
            ['code' => 'BI', 'name' => 'Burundi', 'phone_code' => '+257', 'flag' => '🇧🇮'],
            // C
            ['code' => 'KH', 'name' => 'Cambodia', 'phone_code' => '+855', 'flag' => '🇰🇭'],
            ['code' => 'CM', 'name' => 'Cameroon', 'phone_code' => '+237', 'flag' => '🇨🇲'],
            ['code' => 'CA', 'name' => 'Canada', 'phone_code' => '+1', 'flag' => '🇨🇦'],
            ['code' => 'CV', 'name' => 'Cape Verde', 'phone_code' => '+238', 'flag' => '🇨🇻'],
            ['code' => 'CF', 'name' => 'Central African Republic', 'phone_code' => '+236', 'flag' => '🇨🇫'],
            ['code' => 'TD', 'name' => 'Chad', 'phone_code' => '+235', 'flag' => '🇹🇩'],
            ['code' => 'CL', 'name' => 'Chile', 'phone_code' => '+56', 'flag' => '🇨🇱'],
            ['code' => 'CN', 'name' => 'China', 'phone_code' => '+86', 'flag' => '🇨🇳'],
            ['code' => 'CO', 'name' => 'Colombia', 'phone_code' => '+57', 'flag' => '🇨🇴'],
            ['code' => 'KM', 'name' => 'Comoros', 'phone_code' => '+269', 'flag' => '🇰🇲'],
            ['code' => 'CG', 'name' => 'Congo', 'phone_code' => '+242', 'flag' => '🇨🇬'],
            ['code' => 'CR', 'name' => 'Costa Rica', 'phone_code' => '+506', 'flag' => '🇨🇷'],
            ['code' => 'HR', 'name' => 'Croatia', 'phone_code' => '+385', 'flag' => '🇭🇷'],
            ['code' => 'CU', 'name' => 'Cuba', 'phone_code' => '+53', 'flag' => '🇨🇺'],
            ['code' => 'CY', 'name' => 'Cyprus', 'phone_code' => '+357', 'flag' => '🇨🇾'],
            ['code' => 'CZ', 'name' => 'Czech Republic', 'phone_code' => '+420', 'flag' => '🇨🇿'],
            // D
            ['code' => 'DK', 'name' => 'Denmark', 'phone_code' => '+45', 'flag' => '🇩🇰'],
            ['code' => 'DJ', 'name' => 'Djibouti', 'phone_code' => '+253', 'flag' => '🇩🇯'],
            ['code' => 'DM', 'name' => 'Dominica', 'phone_code' => '+1767', 'flag' => '🇩🇲'],
            ['code' => 'DO', 'name' => 'Dominican Republic', 'phone_code' => '+1809', 'flag' => '🇩🇴'],
            // E
            ['code' => 'EC', 'name' => 'Ecuador', 'phone_code' => '+593', 'flag' => '🇪🇨'],
            ['code' => 'EG', 'name' => 'Egypt', 'phone_code' => '+20', 'flag' => '🇪🇬'],
            ['code' => 'SV', 'name' => 'El Salvador', 'phone_code' => '+503', 'flag' => '🇸🇻'],
            ['code' => 'GQ', 'name' => 'Equatorial Guinea', 'phone_code' => '+240', 'flag' => '🇬🇶'],
            ['code' => 'ER', 'name' => 'Eritrea', 'phone_code' => '+291', 'flag' => '🇪🇷'],
            ['code' => 'EE', 'name' => 'Estonia', 'phone_code' => '+372', 'flag' => '🇪🇪'],
            ['code' => 'ET', 'name' => 'Ethiopia', 'phone_code' => '+251', 'flag' => '🇪🇹'],
            // F
            ['code' => 'FJ', 'name' => 'Fiji', 'phone_code' => '+679', 'flag' => '🇫🇯'],
            ['code' => 'FI', 'name' => 'Finland', 'phone_code' => '+358', 'flag' => '🇫🇮'],
            ['code' => 'FR', 'name' => 'France', 'phone_code' => '+33', 'flag' => '🇫🇷'],
            // G
            ['code' => 'GA', 'name' => 'Gabon', 'phone_code' => '+241', 'flag' => '🇬🇦'],
            ['code' => 'GM', 'name' => 'Gambia', 'phone_code' => '+220', 'flag' => '🇬🇲'],
            ['code' => 'GE', 'name' => 'Georgia', 'phone_code' => '+995', 'flag' => '🇬🇪'],
            ['code' => 'DE', 'name' => 'Germany', 'phone_code' => '+49', 'flag' => '🇩🇪'],
            ['code' => 'GH', 'name' => 'Ghana', 'phone_code' => '+233', 'flag' => '🇬🇭'],
            ['code' => 'GR', 'name' => 'Greece', 'phone_code' => '+30', 'flag' => '🇬🇷'],
            ['code' => 'GD', 'name' => 'Grenada', 'phone_code' => '+1473', 'flag' => '🇬🇩'],
            ['code' => 'GT', 'name' => 'Guatemala', 'phone_code' => '+502', 'flag' => '🇬🇹'],
            ['code' => 'GN', 'name' => 'Guinea', 'phone_code' => '+224', 'flag' => '🇬🇳'],
            ['code' => 'GW', 'name' => 'Guinea-Bissau', 'phone_code' => '+245', 'flag' => '🇬🇼'],
            ['code' => 'GY', 'name' => 'Guyana', 'phone_code' => '+592', 'flag' => '🇬🇾'],
            // H
            ['code' => 'HT', 'name' => 'Haiti', 'phone_code' => '+509', 'flag' => '🇭🇹'],
            ['code' => 'HN', 'name' => 'Honduras', 'phone_code' => '+504', 'flag' => '🇭🇳'],
            ['code' => 'HU', 'name' => 'Hungary', 'phone_code' => '+36', 'flag' => '🇭🇺'],
            // I
            ['code' => 'IS', 'name' => 'Iceland', 'phone_code' => '+354', 'flag' => '🇮🇸'],
            ['code' => 'IN', 'name' => 'India', 'phone_code' => '+91', 'flag' => '🇮🇳'],
            ['code' => 'ID', 'name' => 'Indonesia', 'phone_code' => '+62', 'flag' => '🇮🇩'],
            ['code' => 'IR', 'name' => 'Iran', 'phone_code' => '+98', 'flag' => '🇮🇷'],
            ['code' => 'IQ', 'name' => 'Iraq', 'phone_code' => '+964', 'flag' => '🇮🇶'],
            ['code' => 'IE', 'name' => 'Ireland', 'phone_code' => '+353', 'flag' => '🇮🇪'],
            ['code' => 'IL', 'name' => 'Israel', 'phone_code' => '+972', 'flag' => '🇮🇱'],
            ['code' => 'IT', 'name' => 'Italy', 'phone_code' => '+39', 'flag' => '🇮🇹'],
            ['code' => 'CI', 'name' => 'Ivory Coast', 'phone_code' => '+225', 'flag' => '🇨🇮'],
            // J
            ['code' => 'JM', 'name' => 'Jamaica', 'phone_code' => '+1876', 'flag' => '🇯🇲'],
            ['code' => 'JP', 'name' => 'Japan', 'phone_code' => '+81', 'flag' => '🇯🇵'],
            ['code' => 'JO', 'name' => 'Jordan', 'phone_code' => '+962', 'flag' => '🇯🇴'],
            // K
            ['code' => 'KZ', 'name' => 'Kazakhstan', 'phone_code' => '+7', 'flag' => '🇰🇿'],
            ['code' => 'KE', 'name' => 'Kenya', 'phone_code' => '+254', 'flag' => '🇰🇪'],
            ['code' => 'KI', 'name' => 'Kiribati', 'phone_code' => '+686', 'flag' => '🇰🇮'],
            ['code' => 'KW', 'name' => 'Kuwait', 'phone_code' => '+965', 'flag' => '🇰🇼'],
            ['code' => 'KG', 'name' => 'Kyrgyzstan', 'phone_code' => '+996', 'flag' => '🇰🇬'],
            // L
            ['code' => 'LA', 'name' => 'Laos', 'phone_code' => '+856', 'flag' => '🇱🇦'],
            ['code' => 'LV', 'name' => 'Latvia', 'phone_code' => '+371', 'flag' => '🇱🇻'],
            ['code' => 'LB', 'name' => 'Lebanon', 'phone_code' => '+961', 'flag' => '🇱🇧'],
            ['code' => 'LS', 'name' => 'Lesotho', 'phone_code' => '+266', 'flag' => '🇱🇸'],
            ['code' => 'LR', 'name' => 'Liberia', 'phone_code' => '+231', 'flag' => '🇱🇷'],
            ['code' => 'LY', 'name' => 'Libya', 'phone_code' => '+218', 'flag' => '🇱🇾'],
            ['code' => 'LI', 'name' => 'Liechtenstein', 'phone_code' => '+423', 'flag' => '🇱🇮'],
            ['code' => 'LT', 'name' => 'Lithuania', 'phone_code' => '+370', 'flag' => '🇱🇹'],
            ['code' => 'LU', 'name' => 'Luxembourg', 'phone_code' => '+352', 'flag' => '🇱🇺'],
            // M
            ['code' => 'MG', 'name' => 'Madagascar', 'phone_code' => '+261', 'flag' => '🇲🇬'],
            ['code' => 'MW', 'name' => 'Malawi', 'phone_code' => '+265', 'flag' => '🇲🇼'],
            ['code' => 'MY', 'name' => 'Malaysia', 'phone_code' => '+60', 'flag' => '🇲🇾'],
            ['code' => 'MV', 'name' => 'Maldives', 'phone_code' => '+960', 'flag' => '🇲🇻'],
            ['code' => 'ML', 'name' => 'Mali', 'phone_code' => '+223', 'flag' => '🇲🇱'],
            ['code' => 'MT', 'name' => 'Malta', 'phone_code' => '+356', 'flag' => '🇲🇹'],
            ['code' => 'MH', 'name' => 'Marshall Islands', 'phone_code' => '+692', 'flag' => '🇲🇭'],
            ['code' => 'MR', 'name' => 'Mauritania', 'phone_code' => '+222', 'flag' => '🇲🇷'],
            ['code' => 'MU', 'name' => 'Mauritius', 'phone_code' => '+230', 'flag' => '🇲🇺'],
            ['code' => 'MX', 'name' => 'Mexico', 'phone_code' => '+52', 'flag' => '🇲🇽'],
            ['code' => 'FM', 'name' => 'Micronesia', 'phone_code' => '+691', 'flag' => '🇫🇲'],
            ['code' => 'MD', 'name' => 'Moldova', 'phone_code' => '+373', 'flag' => '🇲🇩'],
            ['code' => 'MC', 'name' => 'Monaco', 'phone_code' => '+377', 'flag' => '🇲🇨'],
            ['code' => 'MN', 'name' => 'Mongolia', 'phone_code' => '+976', 'flag' => '🇲🇳'],
            ['code' => 'ME', 'name' => 'Montenegro', 'phone_code' => '+382', 'flag' => '🇲🇪'],
            ['code' => 'MA', 'name' => 'Morocco', 'phone_code' => '+212', 'flag' => '🇲🇦'],
            ['code' => 'MZ', 'name' => 'Mozambique', 'phone_code' => '+258', 'flag' => '🇲🇿'],
            ['code' => 'MM', 'name' => 'Myanmar', 'phone_code' => '+95', 'flag' => '🇲🇲'],
            // N
            ['code' => 'NA', 'name' => 'Namibia', 'phone_code' => '+264', 'flag' => '🇳🇦'],
            ['code' => 'NR', 'name' => 'Nauru', 'phone_code' => '+674', 'flag' => '🇳🇷'],
            ['code' => 'NP', 'name' => 'Nepal', 'phone_code' => '+977', 'flag' => '🇳🇵'],
            ['code' => 'NL', 'name' => 'Netherlands', 'phone_code' => '+31', 'flag' => '🇳🇱'],
            ['code' => 'NZ', 'name' => 'New Zealand', 'phone_code' => '+64', 'flag' => '🇳🇿'],
            ['code' => 'NI', 'name' => 'Nicaragua', 'phone_code' => '+505', 'flag' => '🇳🇮'],
            ['code' => 'NE', 'name' => 'Niger', 'phone_code' => '+227', 'flag' => '🇳🇪'],
            ['code' => 'NG', 'name' => 'Nigeria', 'phone_code' => '+234', 'flag' => '🇳🇬'],
            ['code' => 'KP', 'name' => 'North Korea', 'phone_code' => '+850', 'flag' => '🇰🇵'],
            ['code' => 'MK', 'name' => 'North Macedonia', 'phone_code' => '+389', 'flag' => '🇲🇰'],
            ['code' => 'NO', 'name' => 'Norway', 'phone_code' => '+47', 'flag' => '🇳🇴'],
            // O
            ['code' => 'OM', 'name' => 'Oman', 'phone_code' => '+968', 'flag' => '🇴🇲'],
            // P
            ['code' => 'PK', 'name' => 'Pakistan', 'phone_code' => '+92', 'flag' => '🇵🇰'],
            ['code' => 'PW', 'name' => 'Palau', 'phone_code' => '+680', 'flag' => '🇵🇼'],
            ['code' => 'PS', 'name' => 'Palestine', 'phone_code' => '+970', 'flag' => '🇵🇸'],
            ['code' => 'PA', 'name' => 'Panama', 'phone_code' => '+507', 'flag' => '🇵🇦'],
            ['code' => 'PG', 'name' => 'Papua New Guinea', 'phone_code' => '+675', 'flag' => '🇵🇬'],
            ['code' => 'PY', 'name' => 'Paraguay', 'phone_code' => '+595', 'flag' => '🇵🇾'],
            ['code' => 'PE', 'name' => 'Peru', 'phone_code' => '+51', 'flag' => '🇵🇪'],
            ['code' => 'PH', 'name' => 'Philippines', 'phone_code' => '+63', 'flag' => '🇵🇭'],
            ['code' => 'PL', 'name' => 'Poland', 'phone_code' => '+48', 'flag' => '🇵🇱'],
            ['code' => 'PT', 'name' => 'Portugal', 'phone_code' => '+351', 'flag' => '🇵🇹'],
            // Q
            ['code' => 'QA', 'name' => 'Qatar', 'phone_code' => '+974', 'flag' => '🇶🇦'],
            // R
            ['code' => 'RO', 'name' => 'Romania', 'phone_code' => '+40', 'flag' => '🇷🇴'],
            ['code' => 'RU', 'name' => 'Russia', 'phone_code' => '+7', 'flag' => '🇷🇺'],
            ['code' => 'RW', 'name' => 'Rwanda', 'phone_code' => '+250', 'flag' => '🇷🇼'],
            // S
            ['code' => 'KN', 'name' => 'Saint Kitts and Nevis', 'phone_code' => '+1869', 'flag' => '🇰🇳'],
            ['code' => 'LC', 'name' => 'Saint Lucia', 'phone_code' => '+1758', 'flag' => '🇱🇨'],
            ['code' => 'VC', 'name' => 'Saint Vincent', 'phone_code' => '+1784', 'flag' => '🇻🇨'],
            ['code' => 'WS', 'name' => 'Samoa', 'phone_code' => '+685', 'flag' => '🇼🇸'],
            ['code' => 'SM', 'name' => 'San Marino', 'phone_code' => '+378', 'flag' => '🇸🇲'],
            ['code' => 'ST', 'name' => 'Sao Tome and Principe', 'phone_code' => '+239', 'flag' => '🇸🇹'],
            ['code' => 'SA', 'name' => 'Saudi Arabia', 'phone_code' => '+966', 'flag' => '🇸🇦'],
            ['code' => 'SN', 'name' => 'Senegal', 'phone_code' => '+221', 'flag' => '🇸🇳'],
            ['code' => 'RS', 'name' => 'Serbia', 'phone_code' => '+381', 'flag' => '🇷🇸'],
            ['code' => 'SC', 'name' => 'Seychelles', 'phone_code' => '+248', 'flag' => '🇸🇨'],
            ['code' => 'SL', 'name' => 'Sierra Leone', 'phone_code' => '+232', 'flag' => '🇸🇱'],
            ['code' => 'SG', 'name' => 'Singapore', 'phone_code' => '+65', 'flag' => '🇸🇬'],
            ['code' => 'SK', 'name' => 'Slovakia', 'phone_code' => '+421', 'flag' => '🇸🇰'],
            ['code' => 'SI', 'name' => 'Slovenia', 'phone_code' => '+386', 'flag' => '🇸🇮'],
            ['code' => 'SB', 'name' => 'Solomon Islands', 'phone_code' => '+677', 'flag' => '🇸🇧'],
            ['code' => 'SO', 'name' => 'Somalia', 'phone_code' => '+252', 'flag' => '🇸🇴'],
            ['code' => 'ZA', 'name' => 'South Africa', 'phone_code' => '+27', 'flag' => '🇿🇦'],
            ['code' => 'KR', 'name' => 'South Korea', 'phone_code' => '+82', 'flag' => '🇰🇷'],
            ['code' => 'SS', 'name' => 'South Sudan', 'phone_code' => '+211', 'flag' => '🇸🇸'],
            ['code' => 'ES', 'name' => 'Spain', 'phone_code' => '+34', 'flag' => '🇪🇸'],
            ['code' => 'LK', 'name' => 'Sri Lanka', 'phone_code' => '+94', 'flag' => '🇱🇰'],
            ['code' => 'SD', 'name' => 'Sudan', 'phone_code' => '+249', 'flag' => '🇸🇩'],
            ['code' => 'SR', 'name' => 'Suriname', 'phone_code' => '+597', 'flag' => '🇸🇷'],
            ['code' => 'SE', 'name' => 'Sweden', 'phone_code' => '+46', 'flag' => '🇸🇪'],
            ['code' => 'CH', 'name' => 'Switzerland', 'phone_code' => '+41', 'flag' => '🇨🇭'],
            ['code' => 'SY', 'name' => 'Syria', 'phone_code' => '+963', 'flag' => '🇸🇾'],
            // T
            ['code' => 'TW', 'name' => 'Taiwan', 'phone_code' => '+886', 'flag' => '🇹🇼'],
            ['code' => 'TJ', 'name' => 'Tajikistan', 'phone_code' => '+992', 'flag' => '🇹🇯'],
            ['code' => 'TZ', 'name' => 'Tanzania', 'phone_code' => '+255', 'flag' => '🇹🇿'],
            ['code' => 'TH', 'name' => 'Thailand', 'phone_code' => '+66', 'flag' => '🇹🇭'],
            ['code' => 'TL', 'name' => 'Timor-Leste', 'phone_code' => '+670', 'flag' => '🇹🇱'],
            ['code' => 'TG', 'name' => 'Togo', 'phone_code' => '+228', 'flag' => '🇹🇬'],
            ['code' => 'TO', 'name' => 'Tonga', 'phone_code' => '+676', 'flag' => '🇹🇴'],
            ['code' => 'TT', 'name' => 'Trinidad and Tobago', 'phone_code' => '+1868', 'flag' => '🇹🇹'],
            ['code' => 'TN', 'name' => 'Tunisia', 'phone_code' => '+216', 'flag' => '🇹🇳'],
            ['code' => 'TR', 'name' => 'Turkey', 'phone_code' => '+90', 'flag' => '🇹🇷'],
            ['code' => 'TM', 'name' => 'Turkmenistan', 'phone_code' => '+993', 'flag' => '🇹🇲'],
            ['code' => 'TV', 'name' => 'Tuvalu', 'phone_code' => '+688', 'flag' => '🇹🇻'],
            // U
            ['code' => 'UG', 'name' => 'Uganda', 'phone_code' => '+256', 'flag' => '🇺🇬'],
            ['code' => 'UA', 'name' => 'Ukraine', 'phone_code' => '+380', 'flag' => '🇺🇦'],
            ['code' => 'AE', 'name' => 'United Arab Emirates', 'phone_code' => '+971', 'flag' => '🇦🇪'],
            ['code' => 'GB', 'name' => 'United Kingdom', 'phone_code' => '+44', 'flag' => '🇬🇧'],
            ['code' => 'US', 'name' => 'United States', 'phone_code' => '+1', 'flag' => '🇺🇸'],
            ['code' => 'UY', 'name' => 'Uruguay', 'phone_code' => '+598', 'flag' => '🇺🇾'],
            ['code' => 'UZ', 'name' => 'Uzbekistan', 'phone_code' => '+998', 'flag' => '🇺🇿'],
            // V
            ['code' => 'VU', 'name' => 'Vanuatu', 'phone_code' => '+678', 'flag' => '🇻🇺'],
            ['code' => 'VA', 'name' => 'Vatican City', 'phone_code' => '+379', 'flag' => '🇻🇦'],
            ['code' => 'VE', 'name' => 'Venezuela', 'phone_code' => '+58', 'flag' => '🇻🇪'],
            ['code' => 'VN', 'name' => 'Vietnam', 'phone_code' => '+84', 'flag' => '🇻🇳'],
            // Y
            ['code' => 'YE', 'name' => 'Yemen', 'phone_code' => '+967', 'flag' => '🇾🇪'],
            // Z
            ['code' => 'ZM', 'name' => 'Zambia', 'phone_code' => '+260', 'flag' => '🇿🇲'],
            ['code' => 'ZW', 'name' => 'Zimbabwe', 'phone_code' => '+263', 'flag' => '🇿🇼'],
        ];
    }
}
