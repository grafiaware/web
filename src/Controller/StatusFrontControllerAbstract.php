<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Controller;


use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Pes\Application\AppFactory;
use Pes\Application\UriInfoInterface;

use Model\Repository\{
    StatusSecurityRepo, StatusFlashRepo, StatusPresentationRepo
};

// pro redirect response
use Pes\Http\Response\RedirectResponse;
use Pes\Http\Response;

/**
 * Description of StatusFrontControllerAbstract
 *
 * @author pes2704
 */
abstract class StatusFrontControllerAbstract extends FrontControllerAbstract implements StatusFrontControllerAbstractInterface {

    /**
     * @var StatusSecurityRepo
     */
    protected $statusSecurityRepo;

    /**
     *
     * @var StatusFlashRepo
     */
    protected $statusFlashRepo;

    /**
     * @var StatusPresentationRepo
     */
    protected $statusPresentationRepo;

    /**
     *
     * @param StatusSecurityRepo $statusSecurityRepo
     */
    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo
            ) {
        $this->statusSecurityRepo = $statusSecurityRepo;
        $this->statusFlashRepo = $statusFlashRepo;
        $this->statusPresentationRepo = $statusPresentationRepo;

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
        $cls = (new \ReflectionClass($this))->getShortName();
        $response = $response->withHeader('X-RED-Controlled', "$cls");
        return $response;
    }

    public function addFlashMessage($message) {
        $this->statusFlashRepo->get()->appendMessage($message);
    }

    /**
     * Vrací base path pro nastavení html base path
     * @param ServerRequestInterface $request
     * @return string
     */
    protected function getBasePath(ServerRequestInterface $request) {
        return $this->getUriInfo($request)->getRootAbsolutePath();
    }

    /**
     * Vrací relativní path pro redirect url
     * @param ServerRequestInterface $request
     * @return type
     */
    protected function getRedirectPath(ServerRequestInterface $request) {
        return $this->getUriInfo($request)->getSubdomainPath();
    }

    /**
     * Pomocná metoda - získá base path z objektu UriInfo, který byl vložen do requestu jako atribut s jménem AppFactory::URI_INFO_ATTRIBUTE_NAME v AppFactory.
     *
     * @return UriInfoInterface
     */
    private function getUriInfo(ServerRequestInterface $request) {
        return $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME);
    }

    /**
     *
     * @param string $relativePath
     * @return Response
     */
    protected function redirectSeeOther(ServerRequestInterface $request, $relativePath) {
        $subPath = $this->getUriInfo($request)->getRootAbsolutePath();
        return RedirectResponse::withPostRedirectGet(new Response(), $subPath.$relativePath); // 303 See Other
    }

    protected function redirectSeeLastGet(ServerRequestInterface $request) {
        return $this->redirectSeeOther($request, $this->statusPresentationRepo->get()->getLastGetResourcePath()); // 303 See Other
    }

}
