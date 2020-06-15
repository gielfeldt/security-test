<?php

// Early bail-out for static files
$doc_root = $_SERVER['DOCUMENT_ROOT'];
$request_uri = $_SERVER["REQUEST_URI"] == '/' ? '/index.html' : $_SERVER["REQUEST_URI"];
$file = "$doc_root$request_uri";
$file = realpath($file);
if (file_exists($file)) {
    return false;
}


// Application starts here
require __DIR__ . '/../vendor/autoload.php';

use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\ErrorLogHandler;
use DI\ContainerBuilder;
use DI\Bridge\Slim\Bridge;
use Psr\Http\Message\ResponseInterface;

// Create PHP-DI container and use in app.
$builder = new ContainerBuilder();
$builder->addDefinitions(
    [
        LoggerInterface::class => function () {
            $logger = new Logger('system');
            $logger->pushHandler(new ErrorLogHandler());
            return $logger;
        }
    ]
);

$container = $builder->build();
$app = Bridge::create($container);

// Setup routes.
$app->get('/api/test', function (LoggerInterface $logger, ResponseInterface $response) {
    $logger->info('Testing');
    $response->getBody()->write('Hello test');
    return $response;
});

// Ready!
$app->run();
