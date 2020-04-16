<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Pes\View\ViewInterface;
/**
 *
 * @author pes2704
 */
interface FrontControllerInterface {

    /**
     *
     * @param \Controller\ServerRequestInterface $request
     * @param \Controller\ViewInterface $view
     * @return ResponseInterface
     */
    public function createResponse(ServerRequestInterface $request, ViewInterface $view): ResponseInterface;

    /**
     *
     * @param \Controller\ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function addHeaders(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface;
}
