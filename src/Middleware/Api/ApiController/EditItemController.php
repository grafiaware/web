<?php

namespace Middleware\Api\ApiController;

use Controller\PresentationFrontControllerAbstract;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Http\Request\RequestParams;
use Pes\Http\Response;

use \Model\Entity\MenuItemInterface;
use GeneratorService\ContentGeneratorRegistryInterface;

use Model\Repository\{
    StatusSecurityRepo, StatusFlashRepo, StatusPresentationRepo, MenuItemRepo
};


/**
 * Description of Controller
 *
 * @author pes2704
 */
class EditItemController extends PresentationFrontControllerAbstract {

    /**
     *
     * @var MenuItemRepo
     */
    private $menuItemRepo;

    /**
     * @var ContentGeneratorRegistryInterface
     */
    private $contentGeneratorRegistry;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            MenuItemRepo $menuItemRepo,
            ContentGeneratorRegistryInterface $contentGeneratorFactory) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);;
        $this->menuItemRepo = $menuItemRepo;
        $this->contentGeneratorRegistry = $contentGeneratorFactory;
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
        $allLangVersionsMenuItems = $this->menuItemRepo->findAllLanguageVersions($uid);
        /** @var MenuItemInterface $langMenuItem */
        $isEmpty = true;
        foreach ($allLangVersionsMenuItems as $langMenuItem) {
            if ($langMenuItem->getTypeFk() != ContentGeneratorRegistryInterface::EMPTY_MENU_ITEM_TYPE) {
                $isEmpty = false;
                user_error("Pokus o nastavení typu položce menu, která již má typ. Položka '{$langMenuItem->getLangCodeFk()}/{$uid}' je typu {$langMenuItem->getTypeFk()}.");
            }
        }
        if ($isEmpty) {
            $contentGenerator = $this->contentGeneratorRegistry->getGenerator($type);
            foreach ($allLangVersionsMenuItems as $langMenuItem) {
                $contentGenerator->initialize($langMenuItem->getId());
                $langMenuItem->setType($type);
            }
            $this->addFlashMessage("menuItem type($type)");
        } else {


        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    private function getMenuItem($uid) {
        return $this->menuItemRepo->get($this->statusPresentationRepo->get()->getLanguage()->getLangCode(), $uid);
    }


}