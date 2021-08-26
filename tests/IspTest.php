<?php

declare(strict_types=1);

namespace GeoIPShim\Tests;

use GeoIPShim\Tests\Shared\BaseTestCase;
use GeoIPShim\Tests\Shared\DataProvider;

class IspTest extends BaseTestCase
{
    private const SOURCE_DATA_FILE = 'GeoIP2-ISP-Test.json';

    public function setUp(): void
    {
        parent::setUp();
        \GeoIPShim\GeoIp2::init([
            dirname(__DIR__, 1) . '/maxmind-db/test-data/GeoIP2-ISP-Test.mmdb',
        ]);
    }

    public function test_geoip_database_info()
    {
        $databaseInfo = geoip_database_info(GEOIP_ISP_EDITION);
        $this->assertEquals('GeoIP2 ISP Test Database (fake GeoIP2 data, for example purposes only)', $databaseInfo);
    }

    public function test_geoip_database_avail()
    {
        $this->assertTrue(geoip_db_avail(GEOIP_ISP_EDITION));
    }

    public function test_geoip_db_filename()
    {
        $this->assertEquals(dirname(__DIR__, 1) . '/maxmind-db/test-data/GeoIP2-ISP-Test.mmdb', geoip_db_filename(GEOIP_ISP_EDITION));
    }

    /**
     * @dataProvider hostnameAsnProvider
     */
    public function test_geoip_asnum_by_name_asn($hostname, $expectedAsnum)
    {
        $asnum = geoip_asnum_by_name($hostname);
        $this->assertSame($expectedAsnum, $asnum);
    }

    public function hostnameAsnProvider()
    {
        $dataProvider = new DataProvider(
            self::SOURCE_DATA_FILE,
            function ($data) { return (string)$data['autonomous_system_number']; }
        );
        yield from $dataProvider->data();
    }

    /**
     * @dataProvider hostnameIspProvider
     */
    public function test_geoip_isp_by_name($hostname, $expecteedIsp)
    {
        $isp = geoip_isp_by_name($hostname);
        $this->assertSame($expecteedIsp, $isp);
    }

    public function hostnameIspProvider()
    {
        $dataProvider = new DataProvider(
            self::SOURCE_DATA_FILE,
            function ($data) { return $data['isp']; }
        );
        yield from $dataProvider->data();
    }

    /**
     * @dataProvider hostnameOrgProvider
     */
    public function test_geoip_org_by_name($hostname, $expectedOrg)
    {
        $org = geoip_org_by_name($hostname);
        $this->assertSame($expectedOrg, $org);
    }

    public function hostnameOrgProvider()
    {
        $dataProvider = new DataProvider(
            self::SOURCE_DATA_FILE,
            function ($data) { return $data['autonomous_system_organization']; }
        );
        yield from $dataProvider->data();
    }
}
