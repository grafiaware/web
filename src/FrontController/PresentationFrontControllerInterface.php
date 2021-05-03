<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FrontController;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Pes\View\ViewInterface;

/**
 *
 * @author pes2704
 */
interface PresentationFrontControllerInterface {

    /**
     *
     * @param \Controller\ServerRequestInterface $request
     * @param \Controller\ViewInterface $view
     * @return ResponseInterface
     */
    public function createResponseFromView(ServerRequestInterface $request, ViewInterface $view): ResponseInterface;
    /**
     *
     * @param ServerRequestInterface $request
     * @param ViewInterface $view
     * @return ResponseInterface
     */
    public function createResponseFromString(ServerRequestInterface $request, $stringContent): ResponseInterface;
}
