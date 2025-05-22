<?php

return [
    'dsn' => getenv('ENQUEUE_DSN') ?: 'redis:',
    'prefix' => getenv('QUEUE_PREFIX') ?: ''
];
