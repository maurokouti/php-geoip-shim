<?php

declare(strict_types=1);

namespace GeoIPShim;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use MaxMind\Db\Reader\InvalidDatabaseException;

final class GeoIp2
{
    private const TYPE_COUNTRY = 'Country';
    private const TYPE_CITY = 'City';
    private const TYPE_ASN = 'ASN';
    private const TYPE_ISP = 'ISP';
    private const TYPE_CONNECTION_TYPE = 'Connection-Type';

    /**
     * @var array<string, Reader>
     */
    private static $dbs = [];

    /**
     * @var array<string, string>
     */
    private static $filenames = [];

    /**
     * @param array<string> $files
     */
    public static function init(array $files): void
    {
        foreach ($files as $file) {
            try {
                $db = new Reader($file);

                switch ($db->metadata()->databaseType) {
                    case 'GeoLite2-Country':
                    case 'GeoIP2-Country':
                        self::$dbs[self::TYPE_COUNTRY] = $db;
                        self::$filenames[self::TYPE_COUNTRY] = $file;

                        break;

                    case 'GeoLite2-City':
                    case 'GeoIP2-City':
                        self::$dbs[self::TYPE_CITY] = $db;
                    self::$filenames[self::TYPE_CITY] = $file;

                        break;

                    case 'GeoLite2-ASN':
                    case 'GeoIP2-ASN':
                        self::$dbs[self::TYPE_ASN] = $db;
                        self::$filenames[self::TYPE_ASN] = $file;

                        break;

                    case 'GeoIP2-ISP':
                        self::$dbs[self::TYPE_ISP] = $db;
                        self::$filenames[self::TYPE_ISP] = $file;

                        break;

                    case 'GeoIP2-Connection-Type':
                        self::$dbs[self::TYPE_CONNECTION_TYPE] = $db;
                        self::$filenames[self::TYPE_CONNECTION_TYPE] = $file;

                        break;
                }
            } catch (InvalidDatabaseException $e) {
            }
        }
    }

    public static function close(): void
    {
        foreach (self::$dbs as $db) {
            $db->close();
        }
        self::$dbs = [];
        self::$filenames = [];
    }

    /**
     * @param string $hostname the hostname or IP address
     *
     * @return false|string returns the ASN on success, or false if the address cannot be found in the database
     */
    public static function geoip_asnum_by_name(string $hostname)
    {
        try {
            if (!$ipAddress = self::getIpAddress($hostname)) {
                return false;
            }
            if (isset(self::$dbs[self::TYPE_ASN])) {
                return (string) self::$dbs[self::TYPE_ASN]->asn($ipAddress)->autonomousSystemNumber;
            }
            if (isset(self::$dbs[self::TYPE_ISP])) {
                return (string) self::$dbs[self::TYPE_ISP]->isp($ipAddress)->autonomousSystemNumber;
            }
        } catch (AddressNotFoundException | InvalidDatabaseException $e) {
            return false;
        }

        return false;
    }

    /**
     * @param string $hostname the hostname or IP address whose location is to be looked-up
     *
     * @return false|string returns the two letter continent code on success, or false if the address cannot be found in the database
     */
    public static function geoip_continent_code_by_name(string $hostname)
    {
        try {
            if (!$ipAddress = self::getIpAddress($hostname)) {
                return false;
            }
            if (isset(self::$dbs[self::TYPE_COUNTRY])) {
                return self::$dbs[self::TYPE_COUNTRY]->country($ipAddress)->continent->code;
            }
            if (isset(self::$dbs[self::TYPE_CITY])) {
                return self::$dbs[self::TYPE_CITY]->city($ipAddress)->continent->code;
            }
        } catch (AddressNotFoundException | InvalidDatabaseException $e) {
            return false;
        }

        return false;
    }

    /**
     * @param string $hostname the hostname or IP address whose location is to be looked-up
     *
     * @return false|string returns the 2-letter ISO country code on success, or false if the address cannot be found in the database
     */
    public static function geoip_country_code_by_name(string $hostname)
    {
        try {
            if (!$ipAddress = self::getIpAddress($hostname)) {
                return false;
            }
            if (isset(self::$dbs[self::TYPE_COUNTRY])) {
                return self::$dbs[self::TYPE_COUNTRY]->country($ipAddress)->country->isoCode;
            }
            if (isset(self::$dbs[self::TYPE_CITY])) {
                return self::$dbs[self::TYPE_CITY]->city($ipAddress)->country->isoCode;
            }
        } catch (AddressNotFoundException | InvalidDatabaseException $e) {
            return false;
        }

        return false;
    }

    /**
     * @param string $hostname the hostname or IP address whose location is to be looked-up
     *
     * @return false|string returns the country name on success, or false if the address cannot be found in the database
     */
    public static function geoip_country_name_by_name(string $hostname)
    {
        try {
            if (!$ipAddress = self::getIpAddress($hostname)) {
                return false;
            }
            if (isset(self::$dbs[self::TYPE_COUNTRY])) {
                return self::$dbs[self::TYPE_COUNTRY]->country($ipAddress)->country->name;
            }
            if (isset(self::$dbs[self::TYPE_CITY])) {
                return self::$dbs[self::TYPE_CITY]->city($ipAddress)->country->name;
            }
        } catch (AddressNotFoundException | InvalidDatabaseException $e) {
            return false;
        }

        return false;
    }

    /**
     * @param int $database the database type as an integer (ie: GEOIP_*_EDITION)
     *
     * @return string|null returns the corresponding database version, or null on error
     */
    public static function geoip_database_info(int $database = GEOIP_COUNTRY_EDITION): ?string
    {
        switch ($database) {
            case GEOIP_COUNTRY_EDITION:
                return isset(self::$dbs[self::TYPE_COUNTRY]) ? self::$dbs[self::TYPE_COUNTRY]->metadata()->description['en'] : null;

            case GEOIP_CITY_EDITION_REV0:
            case GEOIP_CITY_EDITION_REV1:
                return isset(self::$dbs[self::TYPE_CITY]) ? self::$dbs[self::TYPE_CITY]->metadata()->description['en'] : null;

            case GEOIP_ISP_EDITION:
                return isset(self::$dbs[self::TYPE_ISP]) ? self::$dbs[self::TYPE_ISP]->metadata()->description['en'] : null;

            case GEOIP_ASNUM_EDITION:
                return isset(self::$dbs[self::TYPE_ASN]) ? self::$dbs[self::TYPE_ASN]->metadata()->description['en'] : null;

            case GEOIP_NETSPEED_EDITION:
                return isset(self::$dbs[self::TYPE_CONNECTION_TYPE]) ? self::$dbs[self::TYPE_CONNECTION_TYPE]->metadata()->description['en'] : null;
        }

        return null;
    }

    /**
     * @param int $database the database type as an integer (ie: GEOIP_*_EDITION)
     *
     * @return bool|null returns true is database exists, false if not found, or null on error
     */
    public static function geoip_db_avail(int $database): ?bool
    {
        switch ($database) {
            case GEOIP_COUNTRY_EDITION:
                return isset(self::$dbs[self::TYPE_COUNTRY]);

            case GEOIP_CITY_EDITION_REV0:
            case GEOIP_CITY_EDITION_REV1:
                return isset(self::$dbs[self::TYPE_CITY]);

            case GEOIP_ISP_EDITION:
                return isset(self::$dbs[self::TYPE_ISP]);

            case GEOIP_ASNUM_EDITION:
                return isset(self::$dbs[self::TYPE_ASN]);

            case GEOIP_NETSPEED_EDITION:
                return isset(self::$dbs[self::TYPE_CONNECTION_TYPE]);
        }

        return null;
    }

    /**
     * @param int $database the database type as an integer (ie: GEOIP_*_EDITION)
     *
     * @return string|null returns the filename of the corresponding database, or null on error
     */
    public static function geoip_db_filename(int $database): ?string
    {
        switch ($database) {
            case GEOIP_COUNTRY_EDITION:
                return self::$filenames[self::TYPE_COUNTRY] ?? null;

            case GEOIP_CITY_EDITION_REV0:
            case GEOIP_CITY_EDITION_REV1:
                return self::$filenames[self::TYPE_CITY] ?? null;

            case GEOIP_ISP_EDITION:
                return self::$filenames[self::TYPE_ISP] ?? null;

            case GEOIP_ASNUM_EDITION:
                return self::$filenames[self::TYPE_ASN] ?? null;

            case GEOIP_NETSPEED_EDITION:
                return self::$filenames[self::TYPE_CONNECTION_TYPE] ?? null;
        }

        return null;
    }

    /**
     * @phpstan-return array<int, array{'available': bool, 'description': string, 'filename': string}>
     */
    public static function geoip_db_get_all_info(): array
    {
        $result = [];
        foreach (self::$dbs as $type => $db) {
            $result[] = [
                'available' => true,
                'description' => self::$dbs[$type]->metadata()->description['en'],
                'filename' => self::$filenames[$type],
            ];
        }

        return $result;
    }

    /**
     * @param string $hostname the hostname or IP address whose location is to be looked-up
     *
     * @return false|string returns the ISP name on success, or false if the address cannot be found in the database
     */
    public static function geoip_isp_by_name(string $hostname)
    {
        try {
            if (!$ipAddress = self::getIpAddress($hostname)) {
                return false;
            }
            if (isset(self::$dbs[self::TYPE_ISP])) {
                return self::$dbs[self::TYPE_ISP]->isp($ipAddress)->isp;
            }
        } catch (AddressNotFoundException | InvalidDatabaseException $e) {
            return false;
        }

        return false;
    }

    /**
     * @param string $hostname the hostname or IP address
     *
     * @return false|string returns the connection speed on success, or false if the address cannot be found in the database
     */
    public static function geoip_netspeedcell_by_name(string $hostname)
    {
        try {
            if (!$ipAddress = self::getIpAddress($hostname)) {
                return false;
            }
            if (isset(self::$dbs[self::TYPE_CONNECTION_TYPE])) {
                return self::$dbs[self::TYPE_CONNECTION_TYPE]->connectionType($ipAddress)->connectionType;
            }
        } catch (AddressNotFoundException | InvalidDatabaseException $e) {
            return false;
        }

        return false;
    }

    /**
     * @param string $hostname the hostname or IP address
     *
     * @return false|string returns the organization name on success, or false if the address cannot be found in the database
     */
    public static function geoip_org_by_name(string $hostname)
    {
        try {
            if (!$ipAddress = self::getIpAddress($hostname)) {
                return false;
            }
            if (isset(self::$dbs[self::TYPE_ASN])) {
                return self::$dbs[self::TYPE_ASN]->asn($ipAddress)->autonomousSystemOrganization;
            }
            if (isset(self::$dbs[self::TYPE_ISP])) {
                return self::$dbs[self::TYPE_ISP]->isp($ipAddress)->autonomousSystemOrganization;
            }
        } catch (AddressNotFoundException | InvalidDatabaseException $e) {
            return false;
        }

        return false;
    }

    /**
     * @param string $hostname The hostname or IP address whose record is to be looked-up
     *
     * @return array|false
     * @phpstan-return array{'continent_code': string, 'country_code': string, 'country_code3': string, 'country_name':string, 'region':string|false, 'city':string|false, 'postal_code':string|false, 'latitude':float, 'longitude':float, 'dma_code':string, 'area_code':string}|false
     */
    public static function geoip_record_by_name(string $hostname)
    {
        try {
            if (!$ipAddress = self::getIpAddress($hostname)) {
                return false;
            }

            if (isset(self::$dbs[self::TYPE_CITY])) {
                $record = self::$dbs[self::TYPE_CITY]->city($ipAddress);

                return [
                    'continent_code' => $record->continent->code,
                    'country_code' => $record->country->isoCode,
                    'country_code3' => false,
                    'country_name' => $record->country->name,
                    'region' => $record->mostSpecificSubdivision->isoCode ?? false,
                    'city' => $record->city->name ?? false,
                    'postal_code' => $record->postal->code ?? false,
                    'latitude' => $record->location->latitude ?? false,
                    'longitude' => $record->location->longitude ?? false,
                    'dma_code' => false, // not available in GeoIP2
                    'area_code' => false, // not available in GeoIP2
                ];
            }
        } catch (AddressNotFoundException | InvalidDatabaseException $e) {
            return false;
        }

        return false;
    }

    /**
     * @param string $hostname the hostname or IP address whose region is to be looked-up
     *
     * @return array|false
     * @phpstan-return array{'country_code': string, 'region': string}|false
     */
    public static function geoip_region_by_name(string $hostname)
    {
        try {
            if (!$ipAddress = self::getIpAddress($hostname)) {
                return false;
            }
            if (isset(self::$dbs[self::TYPE_CITY])) {
                $countyCode = self::$dbs[self::TYPE_CITY]->city($ipAddress)->country->isoCode;
                $region = self::$dbs[self::TYPE_CITY]->city($ipAddress)->mostSpecificSubdivision->isoCode;

                if (!$region) {
                    return false;
                }

                return [
                    'country_code' => $countyCode,
                    'region' => $region,
                ];
            }
        } catch (AddressNotFoundException | InvalidDatabaseException $e) {
            return false;
        }

        return false;
    }

    /**
     * @return false|string
     */
    private static function getIpAddress(string $hostname)
    {
        return filter_var($hostname, \FILTER_VALIDATE_IP, \FILTER_NULL_ON_FAILURE)
            ?? (($host = gethostbyname($hostname)) !== $hostname ? $host : false);
    }
}
