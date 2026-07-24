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

/**
 * Společný controler pro GET /{module}/v1/static/:menuItemId (red, auth, events).
 *
 * Předá menuItemId z URL do StaticItemViewModel — view model pak načte StaticItem
 * z registry / red DB / session, bez závislosti na předchozím layout requestu.
 *
 * @author pes2704
 */
abstract class StaticComponentControlerAbstract extends ComponentControlerAbstract {

    protected function getActionPermissions(): array {
        return [
            RoleEnum::AUTHENTICATED => [AccessActionEnum::GET => true],
            RoleEnum::ANONYMOUS => [AccessActionEnum::GET => true],
        ];
    }
    
    /**
     * @param mixed $menuItemId Parametr z route (historicky pojmenovaný staticName v auth/events)
     */
    public function static(ServerRequestInterface $request, $menuItemId) {
        if($this->isAllowed(AccessActionEnum::GET)) {
            /** @var StaticItemViewModelInterface $viewModel */
            $viewModel = $this->container->get(StaticItemViewModel::class);
            // Klíčové pro cascade/menuSwap: načtení StaticItem podle ID z URL, ne ze session
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

    /** Base URL aktuálního requestu — pro remote volání template listu / push při stejném hostu. */
    private function resolveBaseUrl(ServerRequestInterface $request): string {
        $scheme = $request->getUri()->getScheme();
        $host = $request->getUri()->getHost();
        $sp = $this->getUriInfo($request)->getSubdomainPath();
        return "$scheme://$host$sp";
    }
}
