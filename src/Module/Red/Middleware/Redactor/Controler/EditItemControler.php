<?php

namespace Red\Middleware\Redactor\Controler;

use FrontControler\FrontControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Http\Request\RequestParams;
use Pes\Http\Response;

use Pes\Text\FriendlyUrl;

use Model\Entity\MenuItemInterface;
use Red\Service\ContentGenerator\ContentGeneratorRegistryInterface;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

use Status\Model\Enum\FlashSeverityEnum;

use Red\Model\Repository\MenuItemRepo;

use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDao;

/**
 * Description of Controler
 *
 * @author pes2704
 */
class EditItemControler extends FrontControlerAbstract {

    /**
     * @var MenuItemRepo
     */
    private $menuItemRepo;


    /**
     * @var ContentGeneratorRegistryInterface
     */
    private $contentGeneratorRegistry;

    /**
     * @var HierarchyAggregateReadonlyDao
     */
    private $hierarchyDao;


    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            MenuItemRepo $menuItemRepo,
            HierarchyAggregateReadonlyDao $hierarchyDao,
            ContentGeneratorRegistryInterface $contentGeneratorFactory
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);;
        $this->menuItemRepo = $menuItemRepo;
        $this->hierarchyDao = $hierarchyDao;
        $this->contentGeneratorRegistry = $contentGeneratorFactory;
    }

    public function toggle(ServerRequestInterface $request, $uid) {
        $menuItem = $this->getMenuItem($uid);
        $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
        $active = $menuItem->getActive();
        if ($active) {
            $subNodes = $this->hierarchyDao->getSubNodes($langCode, $uid);  // včetně "kořene"
            foreach ($subNodes as $node) {
                $menuItem = $this->menuItemRepo->get($langCode, $node['uid']);
                if (isset($menuItem)) {
                    $menuItem->setActive(0);  //active je integer
                }
            }
            $this->addFlashMessage("menuItem toggle(false)", FlashSeverityEnum::SUCCESS);
            if (count($subNodes)>1) {
                $this->addFlashMessage("Item inactivated with all its descendants.", FlashSeverityEnum::WARNING);
            }
        } else {
            $parent = $this->hierarchyDao->getParent($langCode, $uid);
            $parentMenuItem = $this->menuItemRepo->get($langCode, $parent['uid']);
            if (isset($parentMenuItem)AND $parentMenuItem->getActive()) {
                $menuItem->setActive(1);  //active je integer
                $this->addFlashMessage("menuItem toggle(true)", FlashSeverityEnum::SUCCESS);
            } else {
                $this->addFlashMessage("unable to menuItem toggle(true)", FlashSeverityEnum::WARNING);
                $this->addFlashMessage("Parent item is not active.", FlashSeverityEnum::INFO);
            }
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
     }

     /**
      * Nastaví nový titulek a také hodnotu prettyUri.
      *
      *
      * @param ServerRequestInterface $request
      * @param type $uid
      * @return type
      */
    public function title(ServerRequestInterface $request, $uid) {
        $menuItem = $this->getMenuItem($uid);
        $postTitle = (new RequestParams())->getParam($request, 'title');
        $postOriginalTitle = (new RequestParams())->getParam($request, 'original-title');
        $menuItem->setTitle($postTitle);
        // uniquid generuje 13 znaků, pro lang_code rezervuji 3, sloupec prettyUri má 100chars. Limit titulku nastavuji 80. (totéž HierarchyAggregateEditDao)
        $menuItem->setPrettyuri($menuItem->getLangCodeFk().$menuItem->getUidFk().'-'.FriendlyUrl::friendlyUrlText($postTitle, 80));
        $this->addFlashMessage("menuItem title($postTitle)", FlashSeverityEnum::SUCCESS);
        return $this->okMessageResponse("Uložen nový titulek položky menu:".PHP_EOL.$postTitle);
    }

    /**
     * Zkotroluje a nastaví položce menu (menu item) požadovaný typ. Typ menu item před voláním této metody musí být prázdný (typ empty) a pro požadovaný typ musí být zaregistrován generátor
     * obsahu nového prvku v ContentGeneratorRegistry.
     *
     * Metoda nastaví typ menu item a generuje nový obsah pomocí Red\Service\ContentGenerator zaregistrované pro daný typ v ContentGeneratorRegistry.
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
            if ($langMenuItem->getTypeFk()) {
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
            $this->addFlashMessage("menuItem type($type)", FlashSeverityEnum::SUCCESS);
        } else {


        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    private function getMenuItem($uid) {
        return $this->menuItemRepo->get($this->statusPresentationRepo->get()->getLanguage()->getLangCode(), $uid);
    }


}