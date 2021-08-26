<?php

declare(strict_types=1);

namespace GeoIPShim\Tests\Shared;

class BaseTestCase extends \PHPUnit\Framework\TestCase
{
    public function tearDown(): void
    {
        parent::tearDown();
        \GeoIPShim\GeoIp2::close();
    }
}
