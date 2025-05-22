<?php

require __DIR__ . '/../vendor/autoload.php';

use Exporter\MetricsCollector;
use Exporter\TransportFactory;
use Slim\Factory\AppFactory;

$config = require __DIR__ . '/../config/config.php';

$transport = TransportFactory::create($config['dsn'], $config['prefix']);
$collector = new MetricsCollector($transport);

$app = AppFactory::create();

$app->addRoutingMiddleware();
$app->addErrorMiddleware(false, true, true);

$app->get('/metrics', function ($request, $response) use ($collector) {
    $metrics = $collector->collect();

    $response->getBody()->write($metrics);
    return $response->withHeader('Content-Type', 'text/plain');
});

$app->get('/health', function ($request, $response) {
    $response->getBody()->write('OK');
    $response->withHeader('Content-Type', 'text/plain');
    return $response->withStatus(200);
});

$app->run();
