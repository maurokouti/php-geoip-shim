<?php

declare(strict_types=1);

namespace GeoIPShim\Tests;

use GeoIPShim\Tests\Shared\BaseTestCase;
use GeoIPShim\Tests\Shared\DataProvider;

class ConnectionTypeTest extends BaseTestCase
{
    private const SOURCE_DATA_FILE = 'GeoIP2-Connection-Type-Test.json';

    public function setUp(): void
    {
        parent::setUp();
        \GeoIPShim\GeoIp2::init([
            dirname(__DIR__, 1) . '/maxmind-db/test-data/GeoIP2-Connection-Type-Test.mmdb',
        ]);
    }

    public function test_geoip_database_info()
    {
        $databaseInfo = geoip_database_info(GEOIP_NETSPEED_EDITION);
        $this->assertEquals('GeoIP2 Connection Type Test Database (fake GeoIP2 data, for example purposes only)', $databaseInfo);
    }

    public function test_geoip_database_avail()
    {
        $this->assertTrue(geoip_db_avail(GEOIP_NETSPEED_EDITION));
    }

    public function test_geoip_db_filename()
    {
        $this->assertEquals(dirname(__DIR__, 1) . '/maxmind-db/test-data/GeoIP2-Connection-Type-Test.mmdb', geoip_db_filename(GEOIP_NETSPEED_EDITION));
    }

    /**
     * @dataProvider hostnameConnectionTypeProvider
     */
    public function test_geoip_netspeedcell_by_name($hostname, $expectedConnectionType)
    {
        $connectionType = geoip_netspeedcell_by_name($hostname);
        $this->assertSame($expectedConnectionType, $connectionType);
    }

    public function hostnameConnectionTypeProvider()
    {
        $dataProvider = new DataProvider(
            self::SOURCE_DATA_FILE,
            function ($data) { return $data['connection_type']; }
        );
        yield from $dataProvider->data();
    }
}
