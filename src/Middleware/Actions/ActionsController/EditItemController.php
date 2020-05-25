<?php

namespace Middleware\Actions\ActionsController;

use Controller\PresentationFrontControllerAbstract;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Application\AppFactory;
use Pes\Http\Request\RequestParams;
use Pes\Http\Response\RedirectResponse;
use Pes\Http\Response;

use Model\Repository\{
    StatusSecurityRepo, StatusFlashRepo, StatusPresentationRepo, MenuItemRepo
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
        $active = ! $menuItem->getActive();
        $menuItem->setActive($active);
        $this->addFlashMessage("setActive({$active})");
        return RedirectResponse::withPostRedirectGet(new Response(), $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().'www/last/'); // 303 See Other
     }

     public function actual(ServerRequestInterface $request, $id) {
        $menuItem = $this->getMenuItem($id);
        $button = (new RequestParams())->getParam($request, 'button');
        switch ($button) {
            case 'calendar':
                $showTime = (new RequestParams())->getParam($request, 'show');
                $hideTime = (new RequestParams())->getParam($request, 'hide');
                $menuItem->setShowTime($showTime);
                $menuItem->setHideTime($hideTime);
                $this->addFlashMessage("setShowTime($showTime), setHideTime($hideTime)");
                break;
            case 'permanent':
                $menuItem->setShowTime(null);
                $menuItem->setHideTime(null);
                break;
            default:
                $this->addFlashMessage("actual: Error - unknown button name.");
                break;
        }
        return RedirectResponse::withPostRedirectGet(new Response(), $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().'www/last/'); // 303 See Other
     }

    public function title(ServerRequestInterface $request, $id) {
        $menuItem = $this->getMenuItem($id);
        $postTitle = (new RequestParams())->getParam($request, 'title');
        $postOriginalTitle = (new RequestParams())->getParam($request, 'original-title');
        $menuItem->setTitle($postTitle);
        // vracím 200 OK - použití 204 NoContent způsobí, že v jQuery kódu .done(function(data, textStatus, jqXHR) je proměnná data undefined a ani jqXhr objekt neobsahuje vrácený text - jQuery předpoklákládá, že NoContent znamená NoContent
        $response = new Response();
        $response->getBody()->write("Uložen nový text položky menu:".PHP_EOL.$postTitle);
        return $response;
    }

    public function type(ServerRequestInterface $request) {
        $type = (new RequestParams())->getParam($request, 'type');
        $this->getMenuItem($this->statusPresentationRepo->get()->getMenuItem())->setType($type);
        // vracím 200 OK - použití 204 NoContent způsobí, že v jQuery kódu .done(function(data, textStatus, jqXHR) je proměnná data undefined a ani jqXhr objekt neobsahuje vrácený text - jQuery předpoklákládá, že NoContent znamená NoContent
        $response = new Response();
        $response->getBody()->write("Zěmněn typ na $type.");
        return $response;
    }

    private function getMenuItem($id) {
        $this->menuItemRepo->setOnlyPublishedMode(false);  // nebylo by možné zapnout a editovat vypnutý item
        return $this->menuItemRepo->get($this->statusPresentationRepo->get()->getLanguage()->getLangCode(), $id);
    }
}