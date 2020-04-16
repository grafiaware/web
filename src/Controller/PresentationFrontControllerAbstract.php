<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Controller;

use Psr\Http\Message\ServerRequestInterface;

use Psr\Container\ContainerInterface;
use Model\Repository\StatusSecurityRepo;
use Model\Repository\StatusPresentationRepo;
use Model\Entity\StatusPresentation;

use Pes\Application\AppFactory;
use Psr\Http\Message\ResponseInterface;
use Pes\Application\UriInfoInterface;

/**
 * Description of PresentationFrontControllerAbstract
 *
 * @author pes2704
 */
abstract class PresentationFrontControllerAbstract extends SecurityFrontControllerAbstract {

    /**
     * @var StatusPresentation
     */
    protected $statusPresentation;

    /**
     *
     * @param StatusPresentationModelInterface $statusPresentationModel
     * @param ContainerInterface $container
     */
    public function __construct(
            StatusSecurityRepo  $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo) {
        parent::__construct($statusSecurityRepo);
        $this->statusPresentation = $statusPresentationRepo->get();
    }

    public function addHeaders(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {

        $language = $this->statusPresentation->getLanguage();
        $response = $response->withHeader('Content-Language', $language->getLocale());

        $userActions = $this->statusPresentation->getUserActions();
        if ($userActions AND $userActions->isEditableArticle()) {
            $response = $response->withHeader('Cache-Control', 'no-cache');
        } else {
            $response = $response->withHeader('Cache-Control', 'public, max-age=180');
        }
        return $response;
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
}
