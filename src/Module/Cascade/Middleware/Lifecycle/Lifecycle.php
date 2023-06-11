<?php
namespace Cascade\Middleware\Lifecycle;

use Pes\Container\Container;
use Pes\Middleware\AppMiddlewareAbstract;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Container\LifecycleConfigurator;

use Cascade\Middleware\Lifecycle\Controler\CascadeControler;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ResponseTime
 *
 * @author pes2704
 */
class Lifecycle extends AppMiddlewareAbstract implements MiddlewareInterface
{

    private $container;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        // middleware kontejner:
        $this->container = (new LifecycleConfigurator())->configure(new Container($this->getApp()->getAppContainer()));
            $cascadeCtrl = $this->container->get(CascadeControler::class);
            /** @var CascadeControler $cascadeCtrl */
        if($request->getMethod()=='POST') {
            $response = $handler->handle($request);
            $cascadeCtrl->incrementPresentationVersion($request);
        } elseif ($request->getMethod()=='GET') {
            $cascadeCtrl->startVersioningIfNotStarted();
            if ($cascadeCtrl->actualVersionMatch($request)) {
                $response = $cascadeCtrl->notModified($request)->withHeader('ETag', $cascadeCtrl->getVersionString());
            } else {
                $response = $handler->handle($request)->withHeader('ETag', $cascadeCtrl->getVersionString())->withHeader('Cache-Control', 'no-cache');
            }
        }
        return $response;
    }

}
