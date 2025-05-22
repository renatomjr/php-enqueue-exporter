<?php

namespace Exporter\Transport;

use Exporter\Transport\TransportInterface;
use Predis\Client as RedisClient;

class RedisTransport implements TransportInterface
{
    private RedisClient $redis;
    private string $keyPrefix;

    public function __construct(RedisClient $redis, string|null $keyPrefix = null)
    {
        $this->redis = $redis;
        $this->keyPrefix = $keyPrefix;
    }

    public function getQueueNames(): array
    {
        $keys = $this->redis->keys($this->keyPrefix . '*');
        $queues = [];

        foreach ($keys as $key) {
            $type = $this->redis->type($key);
            if ($type  !== 'list') {
                continue;
            }

            $parts = explode($this->keyPrefix, $key, 2);
            if (count($parts) === 2) {
                $queues[] = $parts[1];
            }
        }

        return array_unique($queues);
    }

    public function getQueueMessagesCount(string $queueName): int
    {
        return $this->redis->llen($this->keyPrefix . $queueName);
    }
}
