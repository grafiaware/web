<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Middleware\Xhr\Controller;

use Site\Configuration;

use Controller\PresentationFrontControllerAbstract;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Psr\Container\ContainerInterface;

use StatusManager\{
    StatusManagerInterface, StatusPresentationManagerInterface

};

use Model\Repository\{
    StatusSecurityRepo, StatusFlashRepo, StatusPresentationRepo
};


use Model\Repository\BlockRepo;
use Model\Repository\PaperRepo;

use \GeneratorService\Paper\PaperServiceInterface;

####################
use Pes\View\ViewFactory;

/**
 * Description of GetControler
 *
 * @author pes2704
 */
abstract class XhrControlerAbstract extends PresentationFrontControllerAbstract  implements XhrControlerInterface {

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function injectContainer(ContainerInterface $componentContainer): XhrControlerInterface {
        $this->container = $componentContainer;
        return $this;
    }

    protected function isEditableLayout() {
        $userActions = $this->statusSecurityRepo->get()->getUserActions();
        return $userActions ? $userActions->isEditableLayout() : false;
    }

    protected function isEditableArticle() {
        $userActions = $this->statusSecurityRepo->get()->getUserActions();
        return $userActions ? $userActions->isEditableArticle() : false;
    }

######################################
    /**
     * Přetěžuje addHeaders() z FrontControllerAbstract
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
//    public function addHeaders(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
//        $response = $response->withHeader('Cache-Control', 'public, max-age=180');
//        return $response;
//    }

}
