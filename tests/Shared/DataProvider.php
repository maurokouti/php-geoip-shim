<?php

declare(strict_types=1);

namespace GeoIPShim\Tests\Shared;

class DataProvider
{
    private $sourceData;

    private $dataExtractor;

    public function __construct(string $filename, callable $dataExtractor, $limit = null)
    {
        $path = dirname(__DIR__, 2) . '/maxmind-db/source-data/' . $filename;
        if (is_null($limit) && isset($_ENV['DATA_PROVIDER_LIMIT'])) {
            $limit = intval($_ENV['DATA_PROVIDER_LIMIT']);
        }

        $this->sourceData = array_slice(
            json_decode(file_get_contents($path), true),
            0,
            $limit,
            true
        );

        $this->dataExtractor = $dataExtractor;
    }

    public function data(): \Generator
    {
        foreach ($this->sourceData as $record) {
            foreach ($record as $network => $data) {
                yield [
                    preg_replace('~/\d+$~', '', $network),
                    ($this->dataExtractor)($data),
                ];
            }
        }

        yield ['127.0.0.1', false]; // not in db
        yield ['256.0.0.1', false]; // invalid address
    }
}