<?php

namespace Middleware\Api\ApiController;

use Controller\PresentationFrontControllerAbstract;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Application\AppFactory;
use Pes\Http\Request\RequestParams;
use Pes\Http\Response\RedirectResponse;
use Pes\Http\Response;

use Model\Repository\{
    StatusSecurityRepo, StatusPresentationRepo, MenuItemRepo
};

/**
 * Description of Controller
 *
 * @author pes2704
 */
class EditItemController extends PresentationFrontControllerAbstract {

    private $menuItemRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            MenuItemRepo $menuItemRepo) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);;
        $this->menuItemRepo = $menuItemRepo;
    }

    public function toggle(ServerRequestInterface $request, $id) {
        $menuItem = $this->getMenuItem($id);
        $menuItem->setActive( ! $menuItem->getActive());
        return RedirectResponse::withPostRedirectGet(new Response(), $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().'www/last/'); // 303 See Other
     }

     public function actual(ServerRequestInterface $request, $id, $showTime, $hideTime) {
        $menuItem = $this->getMenuItem($id);
        $menuItem->setShowTime($showTime);
        $menuItem->setHideTime($hideTime);
            return RedirectResponse::withPostRedirectGet(new Response(), $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().'www/last/'); // 303 See Other
     }

    public function title(ServerRequestInterface $request, $id) {
        $menuItem = $this->getMenuItem($id);
        $postTitle = (new RequestParams())->getParam($request, 'title');
        $postOriginalTitle = (new RequestParams())->getParam($request, 'original-title');
        $menuItem->setTitle($postTitle);
        // vracím 200 OK - použití 204 NoContent způsobí, že v jQuery kódu .done(function(data, textStatus, jqXHR) je proměnná data undefined a ani jqXhr objekt neobsahuje vrácený text - jQuery předpoklákládá, že NoContent znamená NoContent
        $response = new Response();
        $response->getBody()->write("Uložen nový název $postTitle.");
        return $response;
    }

    public function type(ServerRequestInterface $request) {
        $type = (new RequestParams())->getParam($request, 'type');
        $this->getMenuItem($this->statusPresentation->getMenuItem())->setType($type);
        // vracím 200 OK - použití 204 NoContent způsobí, že v jQuery kódu .done(function(data, textStatus, jqXHR) je proměnná data undefined a ani jqXhr objekt neobsahuje vrácený text - jQuery předpoklákládá, že NoContent znamená NoContent
        $response = new Response();
        $response->getBody()->write("Zěmněn typ na $type.");
        return $response;
    }

    private function getMenuItem($id) {
        $this->menuItemRepo->setOnlyPublishedMode(false);  // nebylo by možné zapnout a editovat vypnutý item
        return $this->menuItemRepo->get($this->statusPresentation->getLanguage()->getLangCode(), $id);
    }
}