<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace StatusManager;

use Psr\Http\Message\ServerRequestInterface;
use Application\WebAppFactory;
use Site\Configuration;

use Model\Repository\{
    LanguageRepo, MenuRootRepo, MenuItemRepo
};

use Model\Entity\{
    HierarchyNodeInterface, StatusPresentationInterface, LanguageInterface
};

use Model\Entity\{
    StatusPresentation

};

/**
 * Description of PresentationController
 *
 * @author pes2704
 */
class StatusPresentationManager implements StatusPresentationManagerInterface {

    /**
     * @var LanguageRepo
     */
    protected $languageRepo;

    /**
     * @var MenuRootRepo
     */
    protected $menuRootRepo;

    /**
     * @var MenuItemRepo
     */
    protected $menuItemRepo;

    /**
     * @var StatusPresentationInterface
     */
    private $statusPresentation;

    public function __construct(
            LanguageRepo $languageRepo,
            MenuRootRepo $menuRootRepo,
            MenuItemRepo $menuItemRepo
            ) {
        $this->languageRepo = $languageRepo;
        $this->menuRootRepo = $menuRootRepo;
        $this->menuItemRepo = $menuItemRepo;
    }

    /**
     *
     * @return StatusPresentationInterface
     */
    public function createPresentationStatus(): StatusPresentationInterface {
        return new StatusPresentation();
    }

    /**
     * Pokud nejsou nastaveny hodnoty, nastaví defaultní hodnoty language, menuItem do presentation statusu.
     *
     * @param StatusPresentationInterface $statusPresentation
     * @param ServerRequestInterface $request
     * @return void
     */
    public function regenerateStatusPresentation(StatusPresentationInterface $statusPresentation, ServerRequestInterface $request): void {

        ## defaultní hodnoty parametrů status presentation

        // jazyk prezentace
        $language = $statusPresentation->getLanguage();
        if (!isset($language)) {
            $language = $this->getRequestedLanguage($request);
            $statusPresentation->setLanguage($language);
        }
        // defaultní node item pro jazyk prezentace
        $menuItem = $statusPresentation->getMenuItem();
        if (!$menuItem) {
            $menuItem = $this->getDefaulMenuItem($language->getLangCode());
            $statusPresentation->setMenuItem($menuItem);
        }
    }

    /**
     * Default LanguageInterface objekt podle kódu jazyka požadovaného v requestu (z hlavičky Accept-Language) apokud takový jazyk aplikace není
     * v databázi, pak podle konstanty třídy DEFAULT_LANG_CODE
     * @return LanguageInterface
     */
    private function getRequestedLanguage(ServerRequestInterface $request) {
        $requestedLangCode = $request->getAttribute(WebAppFactory::REQUESTED_LANGUAGE_ATTRIBUTE_NAME);
        $language = $this->languageRepo->get($requestedLangCode);
        if (!isset($language)) {
            user_error("Podle hlavičky requestu Accept-Language je požadován kód jazyka $requestedLangCode. "
                    . "Takový kód jazyka nebyl nalezen mezi jazyky aplikace. Nastaven default jazyk aplikace.", E_USER_NOTICE);
            $language = $this->languageRepo->get(Configuration::statusPresentationManager()['default_lang_code']);
        }
        return $language;
    }

    /**
     * Default menu item uid - kořenová položka menu určeného konstantou třídy DEEAULT_HIERARCHY_ROOT_COMPONENT_NAME
     * @return HierarchyNodeInterface
     */
    private function getDefaulMenuItem($langCode) {
        $rootName = Configuration::statusPresentationManager()['default_hierarchy_root_component_name'];
        $menuRootItem = $this->menuRootRepo->get($rootName);
        if (!isset($menuRootItem)) {
            throw new \UnexpectedValueException("Nenalezen default kořen menu se jménem '$rootName' načteným z konfigurace.");
        }
        $uidFk = $menuRootItem->getUidFk();
        return $this->menuItemRepo->get($langCode, $uidFk );    // kořen menu
    }
}
