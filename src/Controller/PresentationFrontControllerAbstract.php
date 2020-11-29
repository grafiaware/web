<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Controller;


use Model\Repository\{
    StatusSecurityRepo, StatusFlashRepo, StatusPresentationRepo
};

use \Pes\Router\Resource\ResourceRegistryInterface;

use Model\Entity\StatusFlash;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Pes\Http\Factory\ResponseFactory;

use Pes\View\ViewInterface;

use Pes\Http\Response;

/**
 * Description of PresentationFrontControllerAbstract
 *
 * @author pes2704
 */
abstract class PresentationFrontControllerAbstract extends StatusFrontControllerAbstract implements PresentationFrontControllerInterface {

    /**
     * @var ResourceRegistryInterface
     */
    protected $resourceRegistry;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            ResourceRegistryInterface $resourceRegistry=null
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->resourceRegistry = $resourceRegistry;
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @param ViewInterface $view
     * @return ResponseInterface
     */
    public function createResponseFromView(ServerRequestInterface $request, ViewInterface $view): ResponseInterface {

        $response = (new ResponseFactory())->createResponse();

        ####  hlavičky  ####
        $response = $this->addHeaders($request, $response);

        ####  body  ####
//        $size = $response->getBody()->write($view);
        $size = $response->getBody()->write($view->getString());
        $response->getBody()->rewind();
        return $response;
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @param ViewInterface $view
     * @return ResponseInterface
     */
    public function createResponseFromString(ServerRequestInterface $request, $stringContent): ResponseInterface {

        $response = (new ResponseFactory())->createResponse();

        ####  hlavičky  ####
        $response = $this->addHeaders($request, $response);

        ####  body  ####
        $size = $response->getBody()->write($stringContent);
        $response->getBody()->rewind();
        return $response;
    }

    protected function okMessageResponse($messageText) {
        // vracím 200 OK - použití 204 NoContent způsobí, že v jQuery kódu .done(function(data, textStatus, jqXHR) je proměnná data undefined a ani jqXhr objekt neobsahuje vrácený text - jQuery předpoklákládá, že NoContent znamená NoContent
        $response = new Response();
        $response->getBody()->write($messageText);
        return $response;
    }
}
