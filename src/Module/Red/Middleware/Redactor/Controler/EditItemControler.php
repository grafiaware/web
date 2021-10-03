<?php

namespace Red\Middleware\Redactor\Controler;

use FrontControler\FrontControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Http\Request\RequestParams;
use Pes\Http\Response;

use Model\Entity\MenuItemInterface;
use GeneratorService\ContentGeneratorRegistryInterface;

use Status\Model\Repository\{
    StatusSecurityRepo, StatusFlashRepo, StatusPresentationRepo
};
use Red\Model\Repository\{
    MenuItemRepo
};

use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDao;
use Red\Model\Entity\HierarchyAggregateInterface;

/**
 * Description of Controler
 *
 * @author pes2704
 */
class EditItemControler extends FrontControlerAbstract {

    /**
     *
     * @var MenuItemRepo
     */
    private $menuItemRepo;

    /**
     *
     * @var HierarchyAggregateReadonlyDao
     */
    private $hierarchyDao;

    /**
     * @var ContentGeneratorRegistryInterface
     */
    private $contentGeneratorRegistry;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            MenuItemRepo $menuItemRepo,
            HierarchyAggregateReadonlyDao $hierarchyDao,
            ContentGeneratorRegistryInterface $contentGeneratorFactory) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);;
        $this->menuItemRepo = $menuItemRepo;
        $this->contentGeneratorRegistry = $contentGeneratorFactory;
        $this->hierarchyDao = $hierarchyDao;
    }

    public function toggle(ServerRequestInterface $request, $uid) {
        $menuItem = $this->getMenuItem($uid);
        $active = $menuItem->getActive();
        if ($active) {
            $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
            $subNodes = $this->hierarchyDao->getSubNodes($langCode, $uid);
            foreach ($subNodes as $node) {
                $menuItem = $this->menuItemRepo->getOutOfContext($langCode, $node['uid']);
                $menuItem->setActive(0);  //active je integer
            }
        } else {
            $menuItem->setActive(1);  //active je integer
        }
        $this->addFlashMessage("menuItem toggle(".($active?'false':'true').")");
        return $this->redirectSeeLastGet($request); // 303 See Other
     }

    public function title(ServerRequestInterface $request, $uid) {
        $menuItem = $this->getMenuItem($uid);
        $postTitle = (new RequestParams())->getParam($request, 'title');
        $postOriginalTitle = (new RequestParams())->getParam($request, 'original-title');
        $menuItem->setTitle($postTitle);
        $menuItem->setPrettyuri($this->friendlyUrl($postTitle));
        $this->addFlashMessage("menuItem title($postTitle)");
        return $this->okMessageResponse("Uložen nový titulek položky menu:".PHP_EOL.$postTitle);
    }

    private function friendlyUrl($nadpis) {
        $url = $nadpis;
        $url = preg_replace('~[^\\pL0-9_]+~u', '-', $url);
        $url = trim($url, "-");
        $url = iconv("utf-8", "us-ascii//TRANSLIT", $url);
        $url = strtolower($url);
        $url = preg_replace('~[^-a-z0-9_]+~', '', $url);
        return $url;
    }

    /**
     * Zkotroluje a nastaví položce menu (menu item) požadovaný typ. Typ menu item před voláním této metody musí být prázdný (typ empty) a pro požadovaný typ musí být zaregistrován generátor
     * obsahu nového prvku v ContentGeneratorRegistry.
     *
     * Metoda nastaví typ menu item a generuje nový obsah pomocí GeneratorService zaregistrované pro daný typ v ContentGeneratorRegistry.
     *
     * @param ServerRequestInterface $request
     * @param string $uid
     * @return Response
     */
    public function type(ServerRequestInterface $request, $uid) {
        $type = (new RequestParams())->getParam($request, 'type');
        $folded = (new RequestParams())->getParam($request, 'folded', false);   //TODO:  dočasně pro static path!!!!
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
                $langMenuItem->setType($type);
                if ($folded) {
                    $langMenuItem->setPrettyuri('folded:'.$folded);   //TODO:  dočasně pro static path!!!!
                } else {
                    $langMenuItem->setPrettyuri($langMenuItem->getLangCodeFk().$langMenuItem->getUidFk());
                }
                $contentGenerator->initialize($langMenuItem->getId());
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