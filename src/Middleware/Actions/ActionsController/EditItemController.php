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

    public function toggle(ServerRequestInterface $request, $uid) {
        $menuItem = $this->getMenuItem($uid);
        $active = $menuItem->getActive() ? 0 : 1;  //active je integer
        $menuItem->setActive($active);
        $this->addFlashMessage("menuItem toggle(".($active?'true':'false').")");
        return RedirectResponse::withPostRedirectGet(new Response(), $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().'www/last/'); // 303 See Other
     }

    public function title(ServerRequestInterface $request, $uid) {
        $menuItem = $this->getMenuItem($uid);
        $postTitle = (new RequestParams())->getParam($request, 'title');
        $postOriginalTitle = (new RequestParams())->getParam($request, 'original-title');
        $menuItem->setTitle($postTitle);
        $this->addFlashMessage("menuItem title($postTitle)");
        return RedirectResponse::withPostRedirectGet(new Response(), $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().'www/last/'); // 303 See Other
    }

    public function type(ServerRequestInterface $request, $uid) {
        $menuItem = $this->getMenuItem($uid);
        $type = (new RequestParams())->getParam($request, 'type');
        $menuItem->setType($type);
        $this->addFlashMessage("menuItem type($type)");
        return RedirectResponse::withPostRedirectGet(new Response(), $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().'www/last/'); // 303 See Other
    }

    private function getMenuItem($uid) {
        return $this->menuItemRepo->get($this->statusPresentationRepo->get()->getLanguage()->getLangCode(), $uid);
    }
}