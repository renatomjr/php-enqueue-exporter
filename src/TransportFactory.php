<?php

namespace Exporter;

use Enqueue\Dsn\Dsn;
use Exporter\Transport\RedisTransport;
use Exporter\Transport\TransportInterface;
use Predis\Client as PredisClient;
use RuntimeException;

class TransportFactory
{
    public static function create(string $dsn, string|null $prefix = null): TransportInterface
    {
        $dsnObj = Dsn::parseFirst($dsn);
        $config = $dsnObj->toArray();

        switch ($dsnObj->getScheme()) {
            case 'redis':
                return self::createRedisTransport($config, $prefix);
            default:
                throw new RuntimeException("Transport \"{$dsnObj->getScheme()}\" is not supported.");
        }
    }

    private static function createRedisTransport(array $config, string|null $prefix): RedisTransport
    {
        $options = $config['predis_options'] ?? [];

        $parameters = array_filter([
            'scheme' => $config['scheme'] ?? null,
            'host' => $config['host'] ?? null,
            'port' => $config['port'] ?? null,
            'password' => $config['password'] ?? null,
            'database' => $config['database'] ?? null,
            'path' => $config['path'] ?? null,
            'async' => $config['async'] ?? null,
            'persistent' => $config['persistent'] ?? null,
            'timeout' => $config['timeout'] ?? null,
            'read_write_timeout' => $config['read_write_timeout'] ?? null,
            'ssl' => $config['ssl'] ?? null,
        ], fn($v) => $v !== null);

        $client = new PredisClient($parameters, $options);

        return new RedisTransport($client, $prefix);
    }
}
