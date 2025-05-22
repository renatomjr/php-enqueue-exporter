<?php

namespace Exporter\Transport;

interface TransportInterface
{
    public function getQueueNames(): array;

    public function getQueueMessagesCount(string $queueName): int;
}
