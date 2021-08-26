<?php

declare(strict_types=1);

namespace GeoIPShim\Tests;

use GeoIPShim\Tests\Shared\BaseTestCase;
use GeoIPShim\Tests\Shared\DataProvider;

class CountryTest extends BaseTestCase
{
    private const SOURCE_DATA_FILE = 'GeoIP2-Country-Test.json';

    public function setUp(): void
    {
        parent::setUp();
        \GeoIPShim\GeoIp2::init([
            dirname(__DIR__, 1) . '/maxmind-db/test-data/GeoIP2-Country-Test.mmdb',
        ]);
    }

    public function test_geoip_database_info()
    {
        $databaseInfo = geoip_database_info(GEOIP_COUNTRY_EDITION);
        $this->assertEquals('GeoIP2 Country Test Database (fake GeoIP2 data, for example purposes only)', $databaseInfo);
    }


    public function test_geoip_database_avail()
    {
        $this->assertTrue(geoip_db_avail(GEOIP_COUNTRY_EDITION));
    }

    public function test_geoip_db_filename()
    {
        $this->assertEquals(dirname(__DIR__, 1) . '/maxmind-db/test-data/GeoIP2-Country-Test.mmdb', geoip_db_filename(GEOIP_COUNTRY_EDITION));
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
}
