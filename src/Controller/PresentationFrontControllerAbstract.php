<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Controller;

use Pes\Application\AppFactory;
use Pes\Application\UriInfoInterface;

use Model\Repository\{
    StatusSecurityRepo, StatusFlashRepo, StatusPresentationRepo
};

use \Pes\Router\Resource\ResourceRegistryInterface;

use Model\Entity\StatusFlash;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Pes\Http\Factory\ResponseFactory;

use Pes\View\ViewInterface;

// pro redirect response
use Pes\Http\Response\RedirectResponse;
use Pes\Http\Response;

/**
 * Description of PresentationFrontControllerAbstract
 *
 * @author pes2704
 */
abstract class PresentationFrontControllerAbstract extends StatusFrontControllerAbstract {

    /**
     * @var StatusPresentationRepo
     */
    protected $statusPresentationRepo;

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
        parent::__construct($statusSecurityRepo, $statusFlashRepo);
        $this->statusPresentationRepo = $statusPresentationRepo;
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
     * Přetěžuje addHeaders() z FrontControllerAbstract
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function addHeaders(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $language = $this->statusPresentationRepo->get()->getLanguage();
        $response = $response->withHeader('Content-Language', $language->getLocale());

        $userActions = $this->statusSecurityRepo->get()->getUserActions();
        if ($userActions AND $userActions->isEditableArticle()) {
            $response = $response->withHeader('Cache-Control', 'no-cache');
        } else {
            $response = $response->withHeader('Cache-Control', 'public, max-age=180');
        }
        return $response;
    }

    public function addFlashMessage($message) {
        $statusFlash = $this->statusFlashRepo->get();
        if ($statusFlash) {
            $message = $statusFlash->getFlash().PHP_EOL.$message;
        } else {
            $statusFlash = new StatusFlash();
            $this->statusFlashRepo->add($statusFlash);
        }
        $statusFlash->setFlash($message);
    }

    /**
     * Vrací base path pro nastavení html base path
     * @param ServerRequestInterface $request
     * @return string
     */
    protected function getBasePath(ServerRequestInterface $request) {
        return $this->getRequestUriInfo($request)->getRootRelativePath();
    }

    /**
     * Vrací relativní path pro redirect url
     * @param ServerRequestInterface $request
     * @return type
     */
    protected function getRedirectPath(ServerRequestInterface $request) {
        return $this->getRequestUriInfo($request)->getSubdomainPath();
    }

    /**
     * Pomocná metoda - získá base path z objektu UriInfo, který byl vložen do requestu jako atribut s jménem AppFactory::URI_INFO_ATTRIBUTE_NAME v AppFactory.
     *
     * @return UriInfoInterface
     */
    private function getRequestUriInfo(ServerRequestInterface $request) {
        return $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME);
    }

    /**
     *
     * @param string $relativePath
     * @return Response
     */
    protected function redirectSeeOther($request, $relativePath) {
        return RedirectResponse::withPostRedirectGet(new Response(), $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().$relativePath); // 303 See Other
    }
}
