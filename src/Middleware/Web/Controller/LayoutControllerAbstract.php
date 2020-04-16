<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Middleware\Web\Controller;

use Controller\PresentationFrontControllerAbstract;
use Psr\Http\Message\ServerRequestInterface;

use Psr\Container\ContainerInterface;

use Model\Repository\StatusSecurityRepo;
use Model\Repository\StatusPresentationRepo;
####################
use Pes\View\ViewFactory;

use Middleware\Login\Controller\LoginLogoutController;
use Component\View\Status\{
    LoginComponent, UserActionComponent
};

/**
 * Description of GetControler
 *
 * @author pes2704
 */
abstract class LayoutControllerAbstract extends PresentationFrontControllerAbstract  implements LayoutControllerInterface  {

    private $viewFactory;

    protected $container;

    public function injectContainer(ContainerInterface $componentContainer): LayoutControllerInterface {
        $this->container = $componentContainer;
        return $this;
    }

    public function __construct(StatusSecurityRepo $statusSecurityRepo, StatusPresentationRepo $statusPresentationRepo, ViewFactory $viewFactory) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo);
        $this->viewFactory = $viewFactory;
    }

    /**
     *
     * @return CompositeView
     */
    protected function getLayoutView(ServerRequestInterface $request) {
        return $this->viewFactory->phpTemplateCompositeView(PROJECT_DIR.'/templates/layout.php',
                [
                    'basePath' => $this->getBasePath($request),
                    'langCode' => $this->statusPresentation->getLanguage()->getLangCode(),
                    'modal' => $this->getModal()
                ]);
    }


    protected function getModal() {
        $user = $this->statusSecurity->getUser();
        if (null != $user AND $user->getRole()) {   // libovolná role
            /** @var UserActionComponent $actionComponent */
            $actionComponent = $this->container->get(UserActionComponent::class);
            $actionComponent->setData(
                    [
                    'editArticle' => $this->statusPresentation->getUserActions()->isEditableArticle(),
                    'editLayout' => $this->statusPresentation->getUserActions()->isEditableLayout(),
                    'userName' => $user->getUserName()
                    ]);
            return $actionComponent;
        } else {
            /** @var LoginComponent $loginComponent */
            $loginComponent = $this->container->get(LoginComponent::class);
            //loginController nepoužívá viewModel, zadávám data
            $loginComponent->setData(["jmenoFieldName" => LoginLogoutController::JMENO_FIELD_NAME, "hesloFieldName" => LoginLogoutController::HESLO_FIELD_NAME]);
            return $loginComponent;
        }
    }
}
