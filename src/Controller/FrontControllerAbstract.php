<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Pes\Http\Factory\ResponseFactory;

use Pes\View\ViewInterface;

/**
 * Description of ControllerAbstract
 *
 * @author pes2704
 */
abstract class FrontControllerAbstract implements FrontControllerInterface {

    const DEFAULT_CONTENT_LANGUAGE = 'cs-CZ';

    /**
     *
     * @param ServerRequestInterface $request
     * @param ViewInterface $view
     * @return ResponseInterface
     */
    public function createResponse(ServerRequestInterface $request, ViewInterface $view): ResponseInterface {
        $response = (new ResponseFactory())->createResponse();

        ####  hlavičky  ####
        $response = $this->addHeaders($request, $response);

        ####  body  ####
        $size = $response->getBody()->write($view->getString());
        $response->getBody()->rewind();
        return $response;
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function addHeaders(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        //TODO: jazyk z accepted language
        return $response->withHeader('Content-Language', self::DEFAULT_CONTENT_LANGUAGE)->withHeader('Cache-Control', 'public, max-age=180');
    }
}
