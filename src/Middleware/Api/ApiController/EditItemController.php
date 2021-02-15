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

use Model\Entity\MenuItemInterface;

use gene
/**
 * Description of Controller
 *
 * @author pes2704
 */
class EditItemController extends PresentationFrontControllerAbstract {

    const EMPTY_MENU_ITEM_TYPE = 'empty';

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
        return $this->redirectSeeLastGet($request); // 303 See Other
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
        $menuItems = $this->menuItemRepo->findAllLanguageVersions($uid);
        /** @var MenuItemInterface $menuItem */
        $isEmpty = false;
        foreach ($menuItems as $menuItem) {
            if ($menuItem->getTypeFk() != self::EMPTY_MENU_ITEM_TYPE) {
                $isEmpty = false;
                $hasType = $menuItem->getTypeFk();
            }
        }
        if (isEmpty()) {

            foreach ($menuItems as $menuItem) {
                $menuItem->setType($type);
            }
            $this->addFlashMessage("menuItem type($type)");
        } else {
            user_error("Pokus o nastavení typu položce menu, která již má typ. Položka $uid je typu $hasType.");

        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    private function getMenuItem($uid) {
        return $this->menuItemRepo->get($this->statusPresentationRepo->get()->getLanguage()->getLangCode(), $uid);
    }


}