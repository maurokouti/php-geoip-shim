<?php

declare(strict_types=1);

namespace GeoIPShim\Tests;

use GeoIPShim\Tests\Shared\DataProvider;

class CityTest extends \GeoIPShim\Tests\Shared\BaseTestCase
{
    private const SOURCE_DATA_FILE = 'GeoIP2-City-Test.json';
    
    public function setUp(): void
    {
        parent::setUp();
        \GeoIPShim\GeoIp2::init([
            dirname(__DIR__, 1) . '/maxmind-db/test-data/GeoIP2-City-Test.mmdb',
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        \GeoIPShim\GeoIp2::close();
    }

    public function test_geoip_database_info()
    {
        $databaseInfo = geoip_database_info(GEOIP_CITY_EDITION_REV0);
        $this->assertEquals('GeoIP2 City Test Database (fake GeoIP2 data, for example purposes only)', $databaseInfo);
        $databaseInfo = geoip_database_info(GEOIP_CITY_EDITION_REV1);
        $this->assertEquals('GeoIP2 City Test Database (fake GeoIP2 data, for example purposes only)', $databaseInfo);
    }

    public function test_geoip_database_avail()
    {
        $this->assertTrue(geoip_db_avail(GEOIP_CITY_EDITION_REV0));
        $this->assertTrue(geoip_db_avail(GEOIP_CITY_EDITION_REV1));
    }

    public function test_geoip_db_filename()
    {
        $this->assertEquals(dirname(__DIR__, 1) . '/maxmind-db/test-data/GeoIP2-City-Test.mmdb', geoip_db_filename(GEOIP_CITY_EDITION_REV0));
        $this->assertEquals(dirname(__DIR__, 1) . '/maxmind-db/test-data/GeoIP2-City-Test.mmdb', geoip_db_filename(GEOIP_CITY_EDITION_REV1));
    }

    /**
     * @dataProvider hostnameCountryCodeProvider
     */
    public function test_geoip_country_code_by_name($hostname, $expectedCountryCode)
    {
        $countryCode = geoip_country_code_by_name($hostname);
        $this->assertSame($expectedCountryCode, $countryCode);
    }

    public function hostnameCountryCodeProvider()
    {
        $dataProvider = new DataProvider(
            self::SOURCE_DATA_FILE,
            function ($data) { return $data['country']['iso_code']; }
        );
        yield from $dataProvider->data();
    }

    /**
     * @dataProvider hostnameCountryNameProvider
     */
    public function test_geoip_country_name_by_name($hostname, $expectedCountryName)
    {
        $countryName = geoip_country_name_by_name($hostname);
        $this->assertSame($expectedCountryName, $countryName);
    }

    public function hostnameCountryNameProvider()
    {
        $dataProvider = new DataProvider(
            self::SOURCE_DATA_FILE,
            function ($data) { return $data['country']['names']['en']; }
        );
        yield from $dataProvider->data();
    }

    /**
     * @dataProvider hostnameContinentCodeProvider
     */
    public function test_geoip_continent_code_by_name($hostname, $expectedContinentCode)
    {
        $continentCode = geoip_continent_code_by_name($hostname);
        $this->assertSame($expectedContinentCode, $continentCode);
    }

    public function hostnameContinentCodeProvider()
    {
        $dataProvider = new DataProvider(
            self::SOURCE_DATA_FILE,
            function ($data) { return $data['continent']['code']; }
        );
        yield from $dataProvider->data();
    }

    /**
     * @dataProvider hostnameRegionProvider
     */
    public function test_geoip_region_by_name($hostname, $expectedRegion)
    {
        $region = geoip_region_by_name($hostname);
        $this->assertSame($expectedRegion, $region);
    }

    public function hostnameRegionProvider()
    {
        $dataProvider = new DataProvider(
            self::SOURCE_DATA_FILE,
            function ($data) {
                return isset($data['subdivisions'])
                    ? [
                        'country_code' => $data['country']['iso_code'],
                        'region' => $data['subdivisions'][count($data['subdivisions']) - 1]['iso_code']
                    ]
                    : false;
            }
        );
        yield from $dataProvider->data();
    }

    /**
     * @dataProvider hostnameRecordProvider
     */
    public function test_geoip_record_by_name($hostname, $expectedRecord)
    {
        $record = geoip_record_by_name($hostname);
        $this->assertSame($expectedRecord, $record);
    }

    public function hostnameRecordProvider()
    {
        $dataProvider = new DataProvider(
            self::SOURCE_DATA_FILE,
            function ($data) {
                return [
                        'continent_code' => $data['continent']['code'],
                        'country_code' => $data['country']['iso_code'],
                        'country_code3' => false, // not available in GeoIP2
                        'country_name' => $data['country']['names']['en'],
                        'region' => $data['subdivisions'] ? $data['subdivisions'][count($data['subdivisions']) - 1]['iso_code'] : false,
                        'city' => $data['city']['names']['en'] ?? false,
                        'postal_code' => $data['postal']['code'] ?? false,
                        'latitude' => $data['location']['latitude'] ? (float)$data['location']['latitude'] : false,
                        'longitude' => $data['location']['longitude'] ? (float)$data['location']['longitude'] : false,
                        'dma_code' => false, // not available in GeoIP2
                        'area_code' => false, // not available in GeoIP2
                    ];
            }
        );
        yield from $dataProvider->data();
    }
}
