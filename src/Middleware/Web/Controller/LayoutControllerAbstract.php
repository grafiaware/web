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

use StatusManager\{
    StatusSecurityManagerInterface, StatusPresentationManagerInterface

};

use Model\Repository\{
    StatusSecurityRepo, StatusFlashRepo, StatusPresentationRepo
};

####################
use Pes\View\ViewFactory;

use Middleware\Login\Controller\LoginLogoutController;
use Component\View\Status\{
    LoginComponent, LogoutComponent, UserActionComponent
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

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            ViewFactory $viewFactory) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->viewFactory = $viewFactory;
    }

    /**
     *
     * @return CompositeView
     */
    protected function getLayoutView(ServerRequestInterface $request) {
        return $this->viewFactory->phpTemplateCompositeView($this->templatesLayout['layout'],
                [
                    'basePath' => $this->getBasePath($request),
                    'langCode' => $this->statusPresentationRepo->get()->getLanguage()->getLangCode(),
                    'modalLoginLogout' => $this->getModalLoginLogout(),
                    'modalUserAction' => $this->getModalUserAction()
                ]);
    }


    protected function getModalLoginLogout() {
        $user = $this->statusSecurityRepo->get()->getUser();
        if (null != $user AND $user->getRole()) {   // libovolná role
            /** @var LogoutComponent $logoutComponent */
            $logoutComponent = $this->container->get(LogoutComponent::class);
            //$logoutComponent nepoužívá viewModel, používá template definovanou v kontejneru - zadávám data pro template
            $logoutComponent->setData(['userName' => $user->getUserName()]);
            return $logoutComponent;
        } else {
            /** @var LoginComponent $loginComponent */
            $loginComponent = $this->container->get(LoginComponent::class);
            //$loginComponent nepoužívá viewModel, používá template definovanou v kontejneru - zadávám data pro template
            $loginComponent->setData(["jmenoFieldName" => LoginLogoutController::JMENO_FIELD_NAME, "hesloFieldName" => LoginLogoutController::HESLO_FIELD_NAME]);
            return $loginComponent;
        }
    }

    protected function isEditableLayout() {
        $userActions = $this->statusSecurityRepo->get()->getUserActions();
        return $userActions ? $userActions->isEditableLayout() : false;
    }

    protected function isEditableArticle() {
        $userActions = $this->statusSecurityRepo->get()->getUserActions();
        return $userActions ? $userActions->isEditableArticle() : false;
    }

    protected function getModalUserAction() {
        $user = $this->statusSecurityRepo->get()->getUser();
        if (null != $user AND $user->getRole()) {   // libovolná role
            /** @var UserActionComponent $actionComponent */
            $actionComponent = $this->container->get(UserActionComponent::class);
            $actionComponent->setData(
                    [
                    'editArticle' => $this->isEditableArticle(),
                    'editLayout' => $this->isEditableLayout(),
                    'userName' => $user->getUserName()
                    ]);
            return $actionComponent;
        } else {

        }
    }
}
