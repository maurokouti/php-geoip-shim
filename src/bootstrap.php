<?php

use GeoIPShim\GeoIp2 as geoip2;

if (!defined('GEOIP_COUNTRY_EDITION')) {
    define('GEOIP_COUNTRY_EDITION', 1);
}

if (!defined('GEOIP_CITY_EDITION_REV1')) {
    define('GEOIP_CITY_EDITION_REV1', 2);
}

if (!defined('GEOIP_ORG_EDITION')) {
    define('GEOIP_ORG_EDITION', 5);
}

if (!defined('GEOIP_ISP_EDITION')) {
    define('GEOIP_ISP_EDITION', 4);
}

if (!defined('GEOIP_CITY_EDITION_REV0')) {
    define('GEOIP_CITY_EDITION_REV0', 6);
}

if (!defined('GEOIP_PROXY_EDITION')) {
    define('GEOIP_PROXY_EDITION', 8);
}

if (!defined('GEOIP_ASNUM_EDITION')) {
    define('GEOIP_ASNUM_EDITION', 9);
}

if (!defined('GEOIP_NETSPEED_EDITION')) {
    define('GEOIP_NETSPEED_EDITION', 10);
}

if (!defined('GEOIP_DOMAIN_EDITION')) {
    define('GEOIP_DOMAIN_EDITION', 11);
}

if (!defined('GEOIP_UNKNOWN_SPEED')) {
    define('GEOIP_UNKNOWN_SPEED', 0);
}

if (!defined('GEOIP_DIALUP_SPEED')) {
    define('GEOIP_DIALUP_SPEED', 1);
}

if (!defined('GEOIP_CABLEDSL_SPEED')) {
    define('GEOIP_CABLEDSL_SPEED', 2);
}

if (!defined('GEOIP_CORPORATE_SPEED')) {
    define('GEOIP_CORPORATE_SPEED', 3);
}

if (!function_exists('geoip_asnum_by_name')) {
    /**
     * @param string $hostname the hostname or IP address
     *
     * @return false|string returns the ASN on success, or false if the address cannot be found in the database
     */
    function geoip_asnum_by_name(string $hostname)
    {
        return geoip2::geoip_asnum_by_name($hostname);
    }
}

if (!function_exists('geoip_continent_code_by_name')) {
    /**
     * @param string $hostname the hostname or IP address whose location is to be looked-up
     *
     * @return false|string returns the two letter continent code on success, or false if the address cannot be found in the database
     */
    function geoip_continent_code_by_name(string $hostname)
    {
        return geoip2::geoip_continent_code_by_name($hostname);
    }
}

if (!function_exists('geoip_country_code_by_name')) {
    /**
     * @param string $hostname the hostname or IP address whose location is to be looked-up
     *
     * @return false|string returns the 2-letter ISO country code on success, or false if the address cannot be found in the database
     */
    function geoip_country_code_by_name(string $hostname)
    {
        return geoip2::geoip_country_code_by_name($hostname);
    }
}

// GeoIP2 database doesn't have Country ISO 3166-1 alpha-3 codes
//if (!function_exists('geoip_country_code3_by_name')) {
//    function geoip_country_code3_by_name($hostname)
//    {
//    }
//}

if (!function_exists('geoip_country_name_by_name')) {
    /**
     * @param string $hostname the hostname or IP address whose location is to be looked-up
     *
     * @return false|string returns the country name on success, or false if the address cannot be found in the database
     */
    function geoip_country_name_by_name(string $hostname)
    {
        return geoip2::geoip_country_name_by_name($hostname);
    }
}

if (!function_exists('geoip_database_info')) {
    /**
     * @param int $database the database type as an integer (ie: GEOIP_*_EDITION)
     *
     * @return string|null returns the corresponding database version, or null on error
     */
    function geoip_database_info(int $database = GEOIP_COUNTRY_EDITION): ?string
    {
        return geoip2::geoip_database_info($database);
    }
}

if (!function_exists('geoip_db_avail')) {
    /**
     * @param int $database the database type as an integer (ie: GEOIP_*_EDITION)
     *
     * @return bool|null returns true is database exists, false if not found, or null on error
     */
    function geoip_db_avail(int $database): ?bool
    {
        return geoip2::geoip_db_avail($database);
    }
}

if (!function_exists('geoip_db_filename')) {
    /**
     * @param int $database the database type as an integer (ie: GEOIP_*_EDITION)
     *
     * @return string|null returns the filename of the corresponding database, or null on error
     */
    function geoip_db_filename(int $database): ?string
    {
        return geoip2::geoip_db_filename($database);
    }
}

if (!function_exists('geoip_db_get_all_info')) {
    /**
     * @phpstan-return array<int, array{'available': bool, 'description': string, 'filename': string}>
     */
    function geoip_db_get_all_info(): array
    {
        return geoip2::geoip_db_get_all_info();
    }
}

// TODO
//if (!function_exists('geoip_domain_by_name')) {
//    function geoip_domain_by_name($hostname)
//    {
//        return geoip2::geoip_domain_by_name($hostname);
//    }
//}

// TODO
//if (!function_exists('geoip_id_by_name')) {
//    function geoip_id_by_name($hostname): int
//    {
//        return geoip2::geoip_id_by_name($hostname);
//    }
//}

if (!function_exists('geoip_isp_by_name')) {
    /**
     * @param string $hostname the hostname or IP address whose location is to be looked-up
     *
     * @return false|string returns the ISP name on success, or false if the address cannot be found in the database
     */
    function geoip_isp_by_name(string $hostname)
    {
        return geoip2::geoip_isp_by_name($hostname);
    }
}

if (!function_exists('geoip_netspeedcell_by_name')) {
    /**
     * @param string $hostname the hostname or IP address
     *
     * @return false|string returns the connection speed on success, or false if the address cannot be found in the database
     */
    function geoip_netspeedcell_by_name(string $hostname)
    {
        return geoip2::geoip_netspeedcell_by_name($hostname);
    }
}

if (!function_exists('geoip_org_by_name')) {
    /**
     * @param string $hostname the hostname or IP address
     *
     * @return false|string returns the organization name on success, or false if the address cannot be found in the database
     */
    function geoip_org_by_name(string $hostname)
    {
        return geoip2::geoip_org_by_name($hostname);
    }
}

if (!function_exists('geoip_record_by_name')) {
    /**
     * @param string $hostname The hostname or IP address whose record is to be looked-up
     *
     * @return array|false
     * @phpstan-return array{'continent_code': string, 'country_code': string, 'country_code3': string, 'country_name':string, 'region':string|false, 'city':string|false, 'postal_code':string|false, 'latitude':float, 'longitude':float, 'dma_code':string, 'area_code':string}|false
     */
    function geoip_record_by_name(string $hostname)
    {
        return geoip2::geoip_record_by_name($hostname);
    }
}

if (!function_exists('geoip_region_by_name')) {
    /**
     * @param string $hostname the hostname or IP address whose region is to be looked-up
     *
     * @return array|false
     * @phpstan-return array{'country_code': string, 'region': string}|false
     */
    function geoip_region_by_name(string $hostname)
    {
        return geoip2::geoip_region_by_name($hostname);
    }
}

// GeoIP2 doesn't support Region name lookup by Country and Region codes
//if (!function_exists('geoip_region_name_by_code')) {
//    function geoip_region_name_by_code($country_code, $region_code)
//    {
//    }
//}

// GeoIP2 requires an explicit path for initialization and doesn't load files from directory.
//if (!function_exists('geoip_setup_custom_directory')) {
//    function geoip_setup_custom_directory($hostname)
//    {
//    }
//}
