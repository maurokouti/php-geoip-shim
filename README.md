# PHP GeoIP Shim #

As MaxMind [retires GeoIP Legacy databases](https://blog.maxmind.com/2020/06/01/retirement-of-geoip-legacy-downloadable-databases-in-may-2022/)
in May 2022 and the fact that `geoip` PHP extension is not supported in PHP 8.0
this library might be useful as a quickfix for the projects with legacy codebase.

## Install via Composer ##

```bash
composer require maurokouti/php-geoip-shim
```

## Usage ##

```php
<?php

\GeoIPShim\GeoIp2::init([
    '/var/lib/GeoIP/GeoIP2-Country.mmdb',
    '/var/lib/GeoIP/GeoIP2-ISP.mmdb',
]);

// by IP address
$ipAddress = '1.2.3.4';
$countryCode = geoip_country_code_by_name($ipAddress);
$ispName = geoip_isp_by_name($ipAddress);

// by hostname
$hostname = 'github.com';
$countryCode = geoip_country_code_by_name($hostname);
$ispName = geoip_isp_by_name($hostname);
```

## Supported functions ##

- `geoip_asnum_by_name(string $hostname): string`
- `geoip_continent_code_by_name(string $hostname): string`
- `geoip_country_code_by_name(string $hostname): string`
- `geoip_country_name_by_name(string $hostname): string`
- `geoip_database_info(int $database = GEOIP_COUNTRY_EDITION): string`
- `geoip_db_avail(int $database): bool`
- `geoip_db_filename(int $database): string`
- `geoip_db_get_all_info(): array`
- `geoip_domain_by_name(string $hostname): string`
- `geoip_isp_by_name(string $hostname): string`
- `geoip_netspeedcell_by_name(string $hostname): string`
- `geoip_org_by_name(string $hostname): string`
- `geoip_record_by_name(string $hostname): array`
- `geoip_region_by_name(string $hostname): array`

## License ##

[MIT](https://github.com/maurokouti/php-geoip-shim/blob/master/LICENSE)