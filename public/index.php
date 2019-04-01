<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use App\HelloWorld;
use function DI\create;
use function DI\get;
use Zend\Diactoros\Response;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$containerBuilder = new ContainerBuilder();
$containerBuilder->useAutowiring(false);
$containerBuilder->useAnnotations(false);
$containerBuilder->addDefinitions([
    HelloWorld::class => create(HelloWorld::class)
    ->constructor(get('Foo'), get('Response')),
    'Foo' => 'bar',
    'Response' => function() {
        return new Response();
    }
]);

$container = $containerBuilder->build();

$routes = \FastRoute\simpleDispatcher(function (\FastRoute\RouteCollector $r) {
    $r->get('/hello', HelloWorld::class);
});

$middlewareQueue = [];
$middlewareQueue[] = new \Middlewares\FastRoute($routes);
$middlewareQueue[] = new \Middlewares\RequestHandler($container);
$requestHandler = new \Relay\Relay($middlewareQueue);
$response = $requestHandler->handle(\Zend\Diactoros\ServerRequestFactory::fromGlobals());

$emitter = new SapiEmitter();
$emitter->emit($response);

