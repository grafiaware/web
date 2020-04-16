<?php

namespace Middleware\Api\ApiController;

use Controller\PresentationFrontControllerAbstract;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Application\AppFactory;
use Pes\Http\Request\RequestParams;
use Pes\Http\Response\RedirectResponse;
use Pes\Http\Response;

use Model\Repository\MenuItemRepo;
use Model\Entity\MenuItemInterface;

/**
 * Description of Controller
 *
 * @author pes2704
 */
class EditItemController  {

    public function toggle(ServerRequestInterface $request, $id) {
            /* @var $menuItemRepo MenuItemRepo */
            $menuItemRepo = $this->container->get(MenuItemRepo::class);
            $presentationStatus = $this->statusSecurityModel->getPresentationStatus();
            $menuItemRepo->setOnlyPublishedMode(false);  // nebylo by možné zapnout vypnutý item
            $menuItem = $menuItemRepo->get($presentationStatus->getLanguage(), $id);
            $menuItem->setActive( ! $menuItem->getActive());
        return RedirectResponse::withPostRedirectGet(new Response(), $this->request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().'www/last/'); // 303 See Other
     }

     public function actual(ServerRequestInterface $request, $id, $showTime, $hideTime) {
            /* @var $menuItemRepo MenuItemRepo */
            $menuItemRepo = $this->container->get(MenuItemRepo::class);
            $presentationStatus = $this->statusSecurityModel->getPresentationStatus();
            $menuItemRepo->setOnlyPublishedMode(false);  // nebylo by možné zapnout vypnutý item
            $menuItem = $menuItemRepo->get($presentationStatus->getLanguage(), $id);
            /* @var $menuItem MenuItemInterface */
            $menuItem->setShowTime($showTime);
            $menuItem->setHideTime($hideTime);
            return RedirectResponse::withPostRedirectGet(new Response(), $this->request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().'www/last/'); // 303 See Other
     }

    public function title(ServerRequestInterface $request, $id) {
            /* @var $menuItemRepo MenuItemRepo */
            $menuItemRepo = $this->container->get(MenuItemRepo::class);
            $presentationStatus = $this->statusSecurityModel->getPresentationStatus();
            $menuItem = $menuItemRepo->get($presentationStatus->getLanguage(), $id);
            $postTitle = (new RequestParams())->getParam($this->request, 'title');
            $postOriginalTitle = (new RequestParams())->getParam($this->request, 'original-title');
            /* @var $menuItem MenuItemInterface */
            $menuItem->setTitle($postTitle);
            // vracím 200 OK - použití 204 NoContent způsobí, že v jQuery kódu .done(function(data, textStatus, jqXHR) je proměnná data undefined a ani jqXhr objekt neobsahuje vrácený text - jQuery předpoklákládá, že NoContent znamená NoContent
            $response = new Response();
            $response->getBody()->write("Uložen nový název $postTitle.");
        return $response;
    }

    public function type() {
            $type = $this->request->getParam('type');
            /** @var MenuItemRepo $menuItemRepo */
            $menuItemRepo = $this->container->get(MenuItemRepo::class);
            $presentationStatus = $this->statusSecurityModel->getPresentationStatus();
            $menuItem = $menuItemRepo->get($presentationStatus->getLanguage(), $presentationStatus->getItemUid())->setType($type);
    }
}