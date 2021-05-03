<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FrontController;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 *
 * @author pes2704
 */
interface StatusFrontControllerAbstractInterface {

    /**
     *
     * @param \Controller\ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function addHeaders(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface;

}
