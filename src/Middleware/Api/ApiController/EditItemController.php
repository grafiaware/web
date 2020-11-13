<?php

namespace Middleware\Api\ApiController;

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

    public function toggle(ServerRequestInterface $request, $uid) {
        $menuItem = $this->getMenuItem($uid);
        $active = $menuItem->getActive() ? 0 : 1;  //active je integer
        $menuItem->setActive($active);
        $this->addFlashMessage("menuItem toggle(".($active?'true':'false').")");
        return $this->redirectSeeOther($request,'www/last'); // 303 See Other
     }

    public function title(ServerRequestInterface $request, $uid) {
        $menuItem = $this->getMenuItem($uid);
        $postTitle = (new RequestParams())->getParam($request, 'title');
        $postOriginalTitle = (new RequestParams())->getParam($request, 'original-title');
        $menuItem->setTitle($postTitle);
        $this->addFlashMessage("menuItem title($postTitle)");
        return $this->okMessageResponse("Uložen nový text položky menu:".PHP_EOL.$postTitle);
    }

    public function type(ServerRequestInterface $request, $uid) {
        $type = (new RequestParams())->getParam($request, 'type');
        foreach ($this->findAllLanguageVersions($uid) as $menuItem) {
            $menuItem->setType($type);
        }
        $this->addFlashMessage("menuItem type($type)");
        $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
        return $this->redirectSeeOther($request, "www/item/$langCode/$uid");
    }

    private function findAllLanguageVersions($uid) {
        return $this->menuItemRepo->findAllLanguageVersions($uid);
    }

    private function getMenuItem($uid) {
        return $this->menuItemRepo->get($this->statusPresentationRepo->get()->getLanguage()->getLangCode(), $uid);
    }

    private function okMessageResponse($messageText) {
        // vracím 200 OK - použití 204 NoContent způsobí, že v jQuery kódu .done(function(data, textStatus, jqXHR) je proměnná data undefined a ani jqXhr objekt neobsahuje vrácený text - jQuery předpoklákládá, že NoContent znamená NoContent
        $response = new Response();
        $response->getBody()->write($messageText);
        return $response;
    }

}