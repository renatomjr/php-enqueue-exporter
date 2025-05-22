<?php

namespace Exporter;

use Exporter\Transport\TransportInterface;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\InMemory;

class MetricsCollector
{
    private TransportInterface $transport;
    private CollectorRegistry $registry;

    public function __construct(TransportInterface $transport)
    {
        $this->transport = $transport;
        $this->registry = new CollectorRegistry(new InMemory());
    }

    public function collect(): string
    {
        $gauge = $this->registry->getOrRegisterGauge(
            'php_enqueue',
            'queue_size',
            'Number of messages in the queue',
            ['queue']
        );

        foreach ($this->transport->getQueueNames() as $queueName) {
            $count = $this->transport->getQueueMessagesCount($queueName);
            $gauge->set($count, [$queueName]);
        }

        $renderer = new RenderTextFormat();
        return $renderer->render($this->registry->getMetricFamilySamples());
    }
}
