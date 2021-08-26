<?php

declare(strict_types=1);

namespace GeoIPShim\Tests;

use GeoIPShim\Tests\Shared\BaseTestCase;
use GeoIPShim\Tests\Shared\DataProvider;

class AsnTest extends BaseTestCase
{
    private const SOURCE_DATA_FILE = 'GeoLite2-ASN-Test.json';

    public function setUp(): void
    {
        parent::setUp();
        \GeoIPShim\GeoIp2::init([
            dirname(__DIR__, 1) . '/maxmind-db/test-data/GeoLite2-ASN-Test.mmdb',
        ]);
    }

    public function test_geoip_database_info()
    {
        $databaseInfo = geoip_database_info(GEOIP_ASNUM_EDITION);
        $this->assertEquals('GeoLite2 ASN Test Database (fake GeoIP2 data, for example purposes only)', $databaseInfo);
    }

    public function test_geoip_database_avail()
    {
        $this->assertTrue(geoip_db_avail(GEOIP_ASNUM_EDITION));
    }

    public function test_geoip_db_filename()
    {
        $this->assertEquals(dirname(__DIR__, 1) . '/maxmind-db/test-data/GeoLite2-ASN-Test.mmdb', geoip_db_filename(GEOIP_ASNUM_EDITION));
    }

    public function test_geoip_db_get_all_info()
    {
        $allInfo = geoip_db_get_all_info();
        $this->assertCount(1, $allInfo);
        $this->assertTrue($allInfo[0]['available']);
        $this->assertEquals('GeoLite2 ASN Test Database (fake GeoIP2 data, for example purposes only)', $allInfo[0]['description']);
        $this->assertEquals(dirname(__DIR__, 1) . '/maxmind-db/test-data/GeoLite2-ASN-Test.mmdb', geoip_db_filename(GEOIP_ASNUM_EDITION), $allInfo[0]['filename']);
    }

    /**
     * @dataProvider hostnameAsnProvider
     */
    public function test_geoip_asnum_by_name($hostname, $expectedAsnum)
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
