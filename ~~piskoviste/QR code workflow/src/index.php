<?php

/* 
 * Důležité bezpečnostní body / nasazení
 * - HTTPS je nutné (OAuth redirect URIs).
 * - Client secrets: ulož do prostředí nebo KMS, ne do repozitáře.
 * - Šifrování tokenů: access/refresh tokeny šifruj (libsodium/OpenSSL).
 * - Token-binding: navrhl jsem, kde v DB lze vázat QR token na session id — doporučuji to udělat (sleduje session_id() při vytvoření QR).
 * - Rate-limity pro /poll endpoint.
 * - CSRF ochrana využívá OAuth state, přidej další ochranu pro interní formuláře.
 */

//  public/index.php — front controller s FastRoute (zjednodušené)

require __DIR__ . '/../vendor/autoload.php';

use QrLogin\Kernel;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\Response;
use FastRoute;

$container = Kernel::buildContainer();
$config = $container->get('config');

$request = ServerRequestFactory::fromGlobals();

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r){
    $r->get('/', function(){ echo 'QR OAuth service'; });
    $r->get('/qr.png', ['QrLogin\\Controllers\\QRController','qrImage']);
    $r->get('/poll', ['QrLogin\\Controllers\\QRController','poll']);
    $r->get('/oauth/start', ['QrLogin\\Controllers\\AuthController','start']);
    $r->get('/callback', ['QrLogin\\Controllers\\AuthController','callback']);
    $r->get('/mobile-login', function(){ /* simple UI page for mobile */ });
});

$routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo 'Not Found';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo 'Method Not Allowed';
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        // handler can be callable or [class,method]
        if (is_array($handler) && count($handler) === 2) {
            [$class, $method] = $handler;
            // instantiate via container if possible
            if (class_exists($class)) {
                $controller = $container->get($class) ?? new $class(...[]);
                $result = $controller->$method(array_merge($_GET, $vars));
                if (is_array($result)) {
                    header('Content-Type: application/json');
                    echo json_encode($result);
                }
            } else {
                http_response_code(500);
                echo 'Controller class not found';
            }
        } elseif (is_callable($handler)) {
            $resp = call_user_func($handler, $vars);
        }
        break;
}