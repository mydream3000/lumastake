<?php

namespace App\Helpers;

use GeoIp2\Database\Reader;
use Illuminate\Support\Facades\Log;

class GeoIpHelper
{
    /**
     * Get country ISO code by IP address
     */
    public static function getCountryCodeByIp(string $ip): ?string
    {
        try {
            $dbPath = storage_path('app/geoip/GeoLite2-City.mmdb');

            if (!file_exists($dbPath)) {
                return null;
            }

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
     */
    public static function getCountryByIp(string $ip): ?string
    {
        try {
            $dbPath = storage_path('app/geoip/GeoLite2-City.mmdb');

            if (!file_exists($dbPath)) {
                Log::warning('GeoLite2 database not found at: ' . $dbPath);
                return null;
            }

            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                return null;
            }

            $reader = new Reader($dbPath);
            $record = $reader->city($ip);

            return $record->country->name ?? null;

        } catch (\GeoIp2\Exception\AddressNotFoundException $e) {
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
     * Get all countries with phone codes
     * Returns array with: code, name, phone_code, flag_class (CSS class for flag-icons)
     */
    public static function getAllCountries(): array
    {
        $rawCountries = [
            ['code' => 'AF', 'name' => 'Afghanistan', 'phone_code' => '+93'],
            ['code' => 'AL', 'name' => 'Albania', 'phone_code' => '+355'],
            ['code' => 'DZ', 'name' => 'Algeria', 'phone_code' => '+213'],
            ['code' => 'AD', 'name' => 'Andorra', 'phone_code' => '+376'],
            ['code' => 'AO', 'name' => 'Angola', 'phone_code' => '+244'],
            ['code' => 'AG', 'name' => 'Antigua and Barbuda', 'phone_code' => '+1268'],
            ['code' => 'AR', 'name' => 'Argentina', 'phone_code' => '+54'],
            ['code' => 'AM', 'name' => 'Armenia', 'phone_code' => '+374'],
            ['code' => 'AU', 'name' => 'Australia', 'phone_code' => '+61'],
            ['code' => 'AT', 'name' => 'Austria', 'phone_code' => '+43'],
            ['code' => 'AZ', 'name' => 'Azerbaijan', 'phone_code' => '+994'],
            ['code' => 'BS', 'name' => 'Bahamas', 'phone_code' => '+1242'],
            ['code' => 'BH', 'name' => 'Bahrain', 'phone_code' => '+973'],
            ['code' => 'BD', 'name' => 'Bangladesh', 'phone_code' => '+880'],
            ['code' => 'BB', 'name' => 'Barbados', 'phone_code' => '+1246'],
            ['code' => 'BY', 'name' => 'Belarus', 'phone_code' => '+375'],
            ['code' => 'BE', 'name' => 'Belgium', 'phone_code' => '+32'],
            ['code' => 'BZ', 'name' => 'Belize', 'phone_code' => '+501'],
            ['code' => 'BJ', 'name' => 'Benin', 'phone_code' => '+229'],
            ['code' => 'BT', 'name' => 'Bhutan', 'phone_code' => '+975'],
            ['code' => 'BO', 'name' => 'Bolivia', 'phone_code' => '+591'],
            ['code' => 'BA', 'name' => 'Bosnia and Herzegovina', 'phone_code' => '+387'],
            ['code' => 'BW', 'name' => 'Botswana', 'phone_code' => '+267'],
            ['code' => 'BR', 'name' => 'Brazil', 'phone_code' => '+55'],
            ['code' => 'BN', 'name' => 'Brunei', 'phone_code' => '+673'],
            ['code' => 'BG', 'name' => 'Bulgaria', 'phone_code' => '+359'],
            ['code' => 'BF', 'name' => 'Burkina Faso', 'phone_code' => '+226'],
            ['code' => 'BI', 'name' => 'Burundi', 'phone_code' => '+257'],
            ['code' => 'KH', 'name' => 'Cambodia', 'phone_code' => '+855'],
            ['code' => 'CM', 'name' => 'Cameroon', 'phone_code' => '+237'],
            ['code' => 'CA', 'name' => 'Canada', 'phone_code' => '+1'],
            ['code' => 'CV', 'name' => 'Cape Verde', 'phone_code' => '+238'],
            ['code' => 'CF', 'name' => 'Central African Republic', 'phone_code' => '+236'],
            ['code' => 'TD', 'name' => 'Chad', 'phone_code' => '+235'],
            ['code' => 'CL', 'name' => 'Chile', 'phone_code' => '+56'],
            ['code' => 'CN', 'name' => 'China', 'phone_code' => '+86'],
            ['code' => 'CO', 'name' => 'Colombia', 'phone_code' => '+57'],
            ['code' => 'KM', 'name' => 'Comoros', 'phone_code' => '+269'],
            ['code' => 'CG', 'name' => 'Congo', 'phone_code' => '+242'],
            ['code' => 'CR', 'name' => 'Costa Rica', 'phone_code' => '+506'],
            ['code' => 'HR', 'name' => 'Croatia', 'phone_code' => '+385'],
            ['code' => 'CU', 'name' => 'Cuba', 'phone_code' => '+53'],
            ['code' => 'CY', 'name' => 'Cyprus', 'phone_code' => '+357'],
            ['code' => 'CZ', 'name' => 'Czech Republic', 'phone_code' => '+420'],
            ['code' => 'DK', 'name' => 'Denmark', 'phone_code' => '+45'],
            ['code' => 'DJ', 'name' => 'Djibouti', 'phone_code' => '+253'],
            ['code' => 'DM', 'name' => 'Dominica', 'phone_code' => '+1767'],
            ['code' => 'DO', 'name' => 'Dominican Republic', 'phone_code' => '+1809'],
            ['code' => 'EC', 'name' => 'Ecuador', 'phone_code' => '+593'],
            ['code' => 'EG', 'name' => 'Egypt', 'phone_code' => '+20'],
            ['code' => 'SV', 'name' => 'El Salvador', 'phone_code' => '+503'],
            ['code' => 'GQ', 'name' => 'Equatorial Guinea', 'phone_code' => '+240'],
            ['code' => 'ER', 'name' => 'Eritrea', 'phone_code' => '+291'],
            ['code' => 'EE', 'name' => 'Estonia', 'phone_code' => '+372'],
            ['code' => 'ET', 'name' => 'Ethiopia', 'phone_code' => '+251'],
            ['code' => 'FJ', 'name' => 'Fiji', 'phone_code' => '+679'],
            ['code' => 'FI', 'name' => 'Finland', 'phone_code' => '+358'],
            ['code' => 'FR', 'name' => 'France', 'phone_code' => '+33'],
            ['code' => 'GA', 'name' => 'Gabon', 'phone_code' => '+241'],
            ['code' => 'GM', 'name' => 'Gambia', 'phone_code' => '+220'],
            ['code' => 'GE', 'name' => 'Georgia', 'phone_code' => '+995'],
            ['code' => 'DE', 'name' => 'Germany', 'phone_code' => '+49'],
            ['code' => 'GH', 'name' => 'Ghana', 'phone_code' => '+233'],
            ['code' => 'GR', 'name' => 'Greece', 'phone_code' => '+30'],
            ['code' => 'GD', 'name' => 'Grenada', 'phone_code' => '+1473'],
            ['code' => 'GT', 'name' => 'Guatemala', 'phone_code' => '+502'],
            ['code' => 'GN', 'name' => 'Guinea', 'phone_code' => '+224'],
            ['code' => 'GW', 'name' => 'Guinea-Bissau', 'phone_code' => '+245'],
            ['code' => 'GY', 'name' => 'Guyana', 'phone_code' => '+592'],
            ['code' => 'HT', 'name' => 'Haiti', 'phone_code' => '+509'],
            ['code' => 'HN', 'name' => 'Honduras', 'phone_code' => '+504'],
            ['code' => 'HK', 'name' => 'Hong Kong', 'phone_code' => '+852'],
            ['code' => 'HU', 'name' => 'Hungary', 'phone_code' => '+36'],
            ['code' => 'IS', 'name' => 'Iceland', 'phone_code' => '+354'],
            ['code' => 'IN', 'name' => 'India', 'phone_code' => '+91'],
            ['code' => 'ID', 'name' => 'Indonesia', 'phone_code' => '+62'],
            ['code' => 'IR', 'name' => 'Iran', 'phone_code' => '+98'],
            ['code' => 'IQ', 'name' => 'Iraq', 'phone_code' => '+964'],
            ['code' => 'IE', 'name' => 'Ireland', 'phone_code' => '+353'],
            ['code' => 'IL', 'name' => 'Israel', 'phone_code' => '+972'],
            ['code' => 'IT', 'name' => 'Italy', 'phone_code' => '+39'],
            ['code' => 'CI', 'name' => 'Ivory Coast', 'phone_code' => '+225'],
            ['code' => 'JM', 'name' => 'Jamaica', 'phone_code' => '+1876'],
            ['code' => 'JP', 'name' => 'Japan', 'phone_code' => '+81'],
            ['code' => 'JO', 'name' => 'Jordan', 'phone_code' => '+962'],
            ['code' => 'KZ', 'name' => 'Kazakhstan', 'phone_code' => '+7'],
            ['code' => 'KE', 'name' => 'Kenya', 'phone_code' => '+254'],
            ['code' => 'KI', 'name' => 'Kiribati', 'phone_code' => '+686'],
            ['code' => 'KW', 'name' => 'Kuwait', 'phone_code' => '+965'],
            ['code' => 'KG', 'name' => 'Kyrgyzstan', 'phone_code' => '+996'],
            ['code' => 'LA', 'name' => 'Laos', 'phone_code' => '+856'],
            ['code' => 'LV', 'name' => 'Latvia', 'phone_code' => '+371'],
            ['code' => 'LB', 'name' => 'Lebanon', 'phone_code' => '+961'],
            ['code' => 'LS', 'name' => 'Lesotho', 'phone_code' => '+266'],
            ['code' => 'LR', 'name' => 'Liberia', 'phone_code' => '+231'],
            ['code' => 'LY', 'name' => 'Libya', 'phone_code' => '+218'],
            ['code' => 'LI', 'name' => 'Liechtenstein', 'phone_code' => '+423'],
            ['code' => 'LT', 'name' => 'Lithuania', 'phone_code' => '+370'],
            ['code' => 'LU', 'name' => 'Luxembourg', 'phone_code' => '+352'],
            ['code' => 'MG', 'name' => 'Madagascar', 'phone_code' => '+261'],
            ['code' => 'MW', 'name' => 'Malawi', 'phone_code' => '+265'],
            ['code' => 'MY', 'name' => 'Malaysia', 'phone_code' => '+60'],
            ['code' => 'MV', 'name' => 'Maldives', 'phone_code' => '+960'],
            ['code' => 'ML', 'name' => 'Mali', 'phone_code' => '+223'],
            ['code' => 'MT', 'name' => 'Malta', 'phone_code' => '+356'],
            ['code' => 'MH', 'name' => 'Marshall Islands', 'phone_code' => '+692'],
            ['code' => 'MR', 'name' => 'Mauritania', 'phone_code' => '+222'],
            ['code' => 'MU', 'name' => 'Mauritius', 'phone_code' => '+230'],
            ['code' => 'MX', 'name' => 'Mexico', 'phone_code' => '+52'],
            ['code' => 'FM', 'name' => 'Micronesia', 'phone_code' => '+691'],
            ['code' => 'MD', 'name' => 'Moldova', 'phone_code' => '+373'],
            ['code' => 'MC', 'name' => 'Monaco', 'phone_code' => '+377'],
            ['code' => 'MN', 'name' => 'Mongolia', 'phone_code' => '+976'],
            ['code' => 'ME', 'name' => 'Montenegro', 'phone_code' => '+382'],
            ['code' => 'MA', 'name' => 'Morocco', 'phone_code' => '+212'],
            ['code' => 'MZ', 'name' => 'Mozambique', 'phone_code' => '+258'],
            ['code' => 'MM', 'name' => 'Myanmar', 'phone_code' => '+95'],
            ['code' => 'NA', 'name' => 'Namibia', 'phone_code' => '+264'],
            ['code' => 'NR', 'name' => 'Nauru', 'phone_code' => '+674'],
            ['code' => 'NP', 'name' => 'Nepal', 'phone_code' => '+977'],
            ['code' => 'NL', 'name' => 'Netherlands', 'phone_code' => '+31'],
            ['code' => 'NZ', 'name' => 'New Zealand', 'phone_code' => '+64'],
            ['code' => 'NI', 'name' => 'Nicaragua', 'phone_code' => '+505'],
            ['code' => 'NE', 'name' => 'Niger', 'phone_code' => '+227'],
            ['code' => 'NG', 'name' => 'Nigeria', 'phone_code' => '+234'],
            ['code' => 'KP', 'name' => 'North Korea', 'phone_code' => '+850'],
            ['code' => 'MK', 'name' => 'North Macedonia', 'phone_code' => '+389'],
            ['code' => 'NO', 'name' => 'Norway', 'phone_code' => '+47'],
            ['code' => 'OM', 'name' => 'Oman', 'phone_code' => '+968'],
            ['code' => 'PK', 'name' => 'Pakistan', 'phone_code' => '+92'],
            ['code' => 'PW', 'name' => 'Palau', 'phone_code' => '+680'],
            ['code' => 'PS', 'name' => 'Palestine', 'phone_code' => '+970'],
            ['code' => 'PA', 'name' => 'Panama', 'phone_code' => '+507'],
            ['code' => 'PG', 'name' => 'Papua New Guinea', 'phone_code' => '+675'],
            ['code' => 'PY', 'name' => 'Paraguay', 'phone_code' => '+595'],
            ['code' => 'PE', 'name' => 'Peru', 'phone_code' => '+51'],
            ['code' => 'PH', 'name' => 'Philippines', 'phone_code' => '+63'],
            ['code' => 'PL', 'name' => 'Poland', 'phone_code' => '+48'],
            ['code' => 'PT', 'name' => 'Portugal', 'phone_code' => '+351'],
            ['code' => 'QA', 'name' => 'Qatar', 'phone_code' => '+974'],
            ['code' => 'RO', 'name' => 'Romania', 'phone_code' => '+40'],
            ['code' => 'RU', 'name' => 'Russia', 'phone_code' => '+7'],
            ['code' => 'RW', 'name' => 'Rwanda', 'phone_code' => '+250'],
            ['code' => 'KN', 'name' => 'Saint Kitts and Nevis', 'phone_code' => '+1869'],
            ['code' => 'LC', 'name' => 'Saint Lucia', 'phone_code' => '+1758'],
            ['code' => 'VC', 'name' => 'Saint Vincent', 'phone_code' => '+1784'],
            ['code' => 'WS', 'name' => 'Samoa', 'phone_code' => '+685'],
            ['code' => 'SM', 'name' => 'San Marino', 'phone_code' => '+378'],
            ['code' => 'ST', 'name' => 'Sao Tome and Principe', 'phone_code' => '+239'],
            ['code' => 'SA', 'name' => 'Saudi Arabia', 'phone_code' => '+966'],
            ['code' => 'SN', 'name' => 'Senegal', 'phone_code' => '+221'],
            ['code' => 'RS', 'name' => 'Serbia', 'phone_code' => '+381'],
            ['code' => 'SC', 'name' => 'Seychelles', 'phone_code' => '+248'],
            ['code' => 'SL', 'name' => 'Sierra Leone', 'phone_code' => '+232'],
            ['code' => 'SG', 'name' => 'Singapore', 'phone_code' => '+65'],
            ['code' => 'SK', 'name' => 'Slovakia', 'phone_code' => '+421'],
            ['code' => 'SI', 'name' => 'Slovenia', 'phone_code' => '+386'],
            ['code' => 'SB', 'name' => 'Solomon Islands', 'phone_code' => '+677'],
            ['code' => 'SO', 'name' => 'Somalia', 'phone_code' => '+252'],
            ['code' => 'ZA', 'name' => 'South Africa', 'phone_code' => '+27'],
            ['code' => 'KR', 'name' => 'South Korea', 'phone_code' => '+82'],
            ['code' => 'SS', 'name' => 'South Sudan', 'phone_code' => '+211'],
            ['code' => 'ES', 'name' => 'Spain', 'phone_code' => '+34'],
            ['code' => 'LK', 'name' => 'Sri Lanka', 'phone_code' => '+94'],
            ['code' => 'SD', 'name' => 'Sudan', 'phone_code' => '+249'],
            ['code' => 'SR', 'name' => 'Suriname', 'phone_code' => '+597'],
            ['code' => 'SE', 'name' => 'Sweden', 'phone_code' => '+46'],
            ['code' => 'CH', 'name' => 'Switzerland', 'phone_code' => '+41'],
            ['code' => 'SY', 'name' => 'Syria', 'phone_code' => '+963'],
            ['code' => 'TW', 'name' => 'Taiwan', 'phone_code' => '+886'],
            ['code' => 'TJ', 'name' => 'Tajikistan', 'phone_code' => '+992'],
            ['code' => 'TZ', 'name' => 'Tanzania', 'phone_code' => '+255'],
            ['code' => 'TH', 'name' => 'Thailand', 'phone_code' => '+66'],
            ['code' => 'TL', 'name' => 'Timor-Leste', 'phone_code' => '+670'],
            ['code' => 'TG', 'name' => 'Togo', 'phone_code' => '+228'],
            ['code' => 'TO', 'name' => 'Tonga', 'phone_code' => '+676'],
            ['code' => 'TT', 'name' => 'Trinidad and Tobago', 'phone_code' => '+1868'],
            ['code' => 'TN', 'name' => 'Tunisia', 'phone_code' => '+216'],
            ['code' => 'TR', 'name' => 'Turkey', 'phone_code' => '+90'],
            ['code' => 'TM', 'name' => 'Turkmenistan', 'phone_code' => '+993'],
            ['code' => 'TV', 'name' => 'Tuvalu', 'phone_code' => '+688'],
            ['code' => 'UG', 'name' => 'Uganda', 'phone_code' => '+256'],
            ['code' => 'UA', 'name' => 'Ukraine', 'phone_code' => '+380'],
            ['code' => 'AE', 'name' => 'United Arab Emirates', 'phone_code' => '+971'],
            ['code' => 'GB', 'name' => 'United Kingdom', 'phone_code' => '+44'],
            ['code' => 'US', 'name' => 'United States', 'phone_code' => '+1'],
            ['code' => 'UY', 'name' => 'Uruguay', 'phone_code' => '+598'],
            ['code' => 'UZ', 'name' => 'Uzbekistan', 'phone_code' => '+998'],
            ['code' => 'VU', 'name' => 'Vanuatu', 'phone_code' => '+678'],
            ['code' => 'VA', 'name' => 'Vatican City', 'phone_code' => '+379'],
            ['code' => 'VE', 'name' => 'Venezuela', 'phone_code' => '+58'],
            ['code' => 'VN', 'name' => 'Vietnam', 'phone_code' => '+84'],
            ['code' => 'YE', 'name' => 'Yemen', 'phone_code' => '+967'],
            ['code' => 'ZM', 'name' => 'Zambia', 'phone_code' => '+260'],
            ['code' => 'ZW', 'name' => 'Zimbabwe', 'phone_code' => '+263'],
        ];

        // Add flag_class for each country (CSS class for flag-icons library)
        return array_map(function ($country) {
            $country['flag_class'] = 'fi fi-' . strtolower($country['code']);
            return $country;
        }, $rawCountries);
    }
}
