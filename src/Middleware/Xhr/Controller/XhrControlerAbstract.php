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
    StatusSecurityManagerInterface, StatusPresentationManagerInterface

};

use Model\Repository\{
    StatusSecurityRepo, StatusFlashRepo, StatusPresentationRepo
};


use Model\Repository\ComponentAggregateRepo;

use Model\Repository\PaperAggregateRepo;
use Model\Entity\ComponentAggregateInterface;
use Model\Entity\PaperAggregateInterface;

####################
use Pes\View\ViewFactory;
use Pes\View\View;
use Pes\View\Template\PhpTemplate;
use Pes\View\Template\InterpolateTemplate;

use Middleware\Login\Controller\LoginLogoutController;
use Component\View\{
    Status\LoginComponent, Status\LogoutComponent, Status\UserActionComponent,
    Flash\FlashComponent
};

/**
 * Description of GetControler
 *
 * @author pes2704
 */
abstract class XhrControlerAbstract extends PresentationFrontControllerAbstract  implements XhrControlerInterface {

    /**
     * @var PaperAggregateRepo
     */
    protected $paperAggregateRepo;

    /**
     * @var ComponentAggregateRepo
     */
    protected $componentAggregateRepo;

    private $viewFactory;

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            PaperAggregateRepo $paperAggregateRepo,
            ComponentAggregateRepo $componentAggregateRepo,
            ViewFactory $viewFactory) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->paperAggregateRepo = $paperAggregateRepo;
        $this->componentAggregateRepo = $componentAggregateRepo;
        $this->viewFactory = $viewFactory;
    }

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
    public function addHeaders(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $response = $response->withHeader('Cache-Control', 'public, max-age=180');
        return $response;
    }

}
