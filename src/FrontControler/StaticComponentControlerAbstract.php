<?php

namespace FrontControler;

use Access\Enum\AccessActionEnum;
use Access\Enum\RoleEnum;
use Component\View\StaticItemComponent;
use Component\View\StaticItemComponentInterface;
use Component\ViewModel\StaticItemViewModel;
use Component\ViewModel\StaticItemViewModelInterface;
use Psr\Http\Message\ServerRequestInterface;
use Red\Model\Enum\AuthoredTypeEnum;

abstract class StaticComponentControlerAbstract extends ComponentControlerAbstract {

    protected function getActionPermissions(): array {
        return [
            RoleEnum::AUTHENTICATED => [AccessActionEnum::GET => true],
            RoleEnum::ANONYMOUS => [AccessActionEnum::GET => true],
        ];
    }
    
    public function static(ServerRequestInterface $request, $menuItemId) {
        if($this->isAllowed(AccessActionEnum::GET)) {
            /** @var StaticItemViewModelInterface $viewModel */
            $viewModel = $this->container->get(StaticItemViewModel::class);
            $viewModel->setMenuItemId((int) $menuItemId);
            $viewModel->setRequestBaseUrl($this->resolveBaseUrl($request));

            /** @var StaticItemComponentInterface $view */
            $view = $this->container->get(StaticItemComponent::class);
            $view->setData($viewModel);
        } else {
            $view =  $this->getNonPermittedContentView(AccessActionEnum::GET, AuthoredTypeEnum::PAPER);
        }
        return $this->createStringOKResponseFromView($view);            
    }

    private function resolveBaseUrl(ServerRequestInterface $request): string {
        $scheme = $request->getUri()->getScheme();
        $host = $request->getUri()->getHost();
        $sp = $this->getUriInfo($request)->getSubdomainPath();
        return "$scheme://$host$sp";
    }
}
