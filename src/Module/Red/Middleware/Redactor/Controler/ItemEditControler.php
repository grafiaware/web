<?php

namespace Red\Middleware\Redactor\Controler;

use FrontControler\FrontControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Http\Request\RequestParams;
use Pes\Http\Response;

use Pes\Text\FriendlyUrl;

use Model\Entity\MenuItemInterface;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

use Status\Model\Enum\FlashSeverityEnum;

use Red\Model\Repository\MenuItemRepo;
use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDao;
use Red\Service\HierarchyManipulator\MenuItemManipulator;
use Red\Service\ItemCreator\ItemCreatorRegistryInterface;
use Red\Service\HierarchyManipulator\MenuItemToggleResultEnum;
use Red\Service\ItemApi\ItemApiService;

use Pes\Type\Exception\ValueNotInEnumException;
use LogicException;

/**
 * Description of Controler
 *
 * @author pes2704
 */
class ItemEditControler extends FrontControlerAbstract {

    const SEPARATOR = '+';
    const TYPE_VARIABLE_NAME = 'content_generator_type';
    
    /**
     * @var MenuItemRepo
     */
    private $menuItemRepo;

    /**
     * @var MenuItemManipulator
     */
    private $menuItemManipulator;

    /**
     * @var ItemCreatorRegistryInterface
     */
    private $itemCreatorRegistry;

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
            MenuItemManipulator $menuItemManipulator,
            ItemCreatorRegistryInterface $contentGeneratorFactory
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);;
        $this->menuItemRepo = $menuItemRepo;
        $this->hierarchyDao = $hierarchyDao;
        $this->menuItemManipulator = $menuItemManipulator;
        $this->itemCreatorRegistry = $contentGeneratorFactory;
    }

    /**
     * Přepne položku pasivní (nepublikovanou) na aktivní (publikovanou) a naopak.
     *
     * Pozor! Nesmí nastat situace kdy pasivní položka menu má aktivní potomky - to by způsobilo chybné načítání celé
     * struktury menu v needitačním režimu (mimo redakčního systému).
     *
     * Pokud je položka aktivní přepne na pasivní i všechny její potomky
     * Pokud je položka pasivní zjití jestli její rodič je aktivní, pokud rodič je aktivní přepne tuto položku na aktivní (potomky nemění),
     * pokud je rodič pasivní nemění nic (ponechá položku pasivní).
     *
     * @param ServerRequestInterface $request
     * @param type $uid
     * @return type
     */
    public function toggle(ServerRequestInterface $request, $uid) {
        $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
        $msg = $this->menuItemManipulator->toggleItems($langCode, $uid);

        try {
            $toggleResult = new MenuItemToggleResultEnum();
            switch ($toggleResult($msg)) {
                case MenuItemToggleResultEnum::DEACTOVATE_ONE:
                    $this->addFlashMessage("menuItem toggle(false)", FlashSeverityEnum::SUCCESS);
                    break;
                case MenuItemToggleResultEnum::DEACTOVATE_WITH_DESCENDANTS:
                    $this->addFlashMessage("menuItem toggle(false)", FlashSeverityEnum::SUCCESS);
                    $this->addFlashMessage("Item inactivated with all its descendants.", FlashSeverityEnum::WARNING);
                    break;
                case MenuItemToggleResultEnum::ACTIVATE_ONE:
                    $this->addFlashMessage("menuItem toggle(true)", FlashSeverityEnum::SUCCESS);
                    break;
                case MenuItemToggleResultEnum::UNABLE_ACTIVATE:
                    $this->addFlashMessage("unable to menuItem toggle(true)", FlashSeverityEnum::WARNING);
                    $this->addFlashMessage("Parent item is not active.", FlashSeverityEnum::INFO);
                    break;
            }
        return $this->createJsonPutOKResponse(["refresh"=>"item", "newitemuid"=>$uid]);
//            return $this->redirectSeeLastGet($request); // 303 See Other

        } catch (ValueNotInEnumException $notInEnumExc) {
            throw new ValueNotInEnumException(" Neznámý výsledek operace menuItemxManipulator->toggleItems()!");
        }
     }

     /**
      * Nastaví nový titulek a také hodnotu prettyUri.
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
        return $this->createJsonPutOKResponse(["refresh"=>"norefresh", "message"=>"Uložen nový titulek položky menu:".PHP_EOL.$postTitle]);
    }

    /**
     * Zkotroluje a nastaví položce menu (menu item) požadovaný typ. Typ menu item před voláním této metody musí být prázdný (typ empty) a pro požadovaný typ musí být zaregistrován generátor
     * obsahu nového prvku v ContentGeneratorRegistry.
     *
     * Metoda nastaví typ menu item a generuje nový obsah pomocí Red\Service\ItemCreator zaregistrované pro daný typ v ContentGeneratorRegistry.
     *
     * @param ServerRequestInterface $request
     * @param string $uid
     * @return Response
     */
    public function type(ServerRequestInterface $request, $uid) {
        $postedType = (new RequestParams())->getParam($request, self::TYPE_VARIABLE_NAME);
        list($postedModule, $postedGenerator) = explode(self::SEPARATOR, $postedType);
        $folded = (new RequestParams())->getParam($request, 'folded', false);   //TODO:  dočasně pro static path!!!!
        $allLangVersionsMenuItems = $this->menuItemRepo->findAllLanguageVersions($uid);
        /** @var MenuItemInterface $langMenuItem */
        foreach ($allLangVersionsMenuItems as $langMenuItem) {
            if (null!==$langMenuItem->getApiModuleFk() AND ItemApiService::DEFAULT_MODULE!=$langMenuItem->getApiModuleFk()
                    OR 
                null!==$langMenuItem->getApiGeneratorFk() AND ItemApiService::DEFAULT_GENERATOR!=$langMenuItem->getApiGeneratorFk()) {
                throw new LogicException("Pokus o nastavení typu položce menu, která již má api modul nebo api generator. "
                        . "Položka '{$langMenuItem->getLangCodeFk()}/{$uid}' má nastaven api modul {$langMenuItem->getApiModuleFk()} a api generátor {$langMenuItem->getApiGeneratorFk()}.");
            }
        }
        $contentGenerator = $this->itemCreatorRegistry->getGenerator($postedModule, $postedGenerator);
        foreach ($allLangVersionsMenuItems as $langMenuItem) {
            $langMenuItem->setApiModuleFk($postedModule);
            $langMenuItem->setApiGeneratorFk($postedGenerator);
            if ($folded) {
                $langMenuItem->setPrettyuri('folded:'.$folded);   //TODO:  dočasně pro static path!!!!
            } else {
                $langMenuItem->setPrettyuri($langMenuItem->getLangCodeFk().$langMenuItem->getUidFk());   //TODO: service propretty uri
            }
            $contentGenerator->initialize($langMenuItem);
        }
        $this->addFlashMessage("menuItem type($postedModule, $postedGenerator)", FlashSeverityEnum::SUCCESS);
        return $this->createJsonPutOKResponse(["refresh"=>"item", "newitemuid"=>$uid]);
        
//        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    private function getMenuItem($uid) {
        return $this->menuItemRepo->get($this->statusPresentationRepo->get()->getLanguage()->getLangCode(), $uid);
    }


}