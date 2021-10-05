<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FrontControler;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Container\ContainerInterface;

use Pes\View\ViewInterface;
/**
 *
 * @author pes2704
 */
interface FrontControlerInterface {

    /**
     *
     * @param \Controller\ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function addHeaders(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface;

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

    /**
     * Generuje response s přesměrováním na zadanou adresu.
     *
     * @param string $restUri Relativní adresa - resource uri
     * @return Response
     */
    public function createResponseRedirectSeeOther(ServerRequestInterface $request, $restUri): ResponseInterface;

    public function addFlashMessage($message): void;

    public function injectContainer(ContainerInterface $componentContainer): FrontControlerInterface;

    public function setConfiguration($configuration): FrontControlerInterface;
}
