<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FrontController;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Container\ContainerInterface;

/**
 * Description of ControllerAbstract
 *
 * @author pes2704
 */
abstract class FrontControllerAbstract implements FrontControllerInterface {


    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function addHeaders(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {

        $response = $response->withHeader('Cache-Control', 'no-cache');

        return $response;
    }

    public function injectContainer(ContainerInterface $componentContainer): FrontControllerInterface {
        $this->container = $componentContainer;
        return $this;
    }
}
