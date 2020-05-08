<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace StatusManager;

use Model\Repository\{
    LanguageRepo, MenuRootRepo, MenuItemRepo
};

use Model\Entity\{
    MenuNodeInterface, StatusPresentationInterface, LanguageInterface
};

use Model\Entity\{
    StatusPresentationInterface, UserActions

};

/**
 * Description of PresentationController
 *
 * @author pes2704
 */
class StatusPresentationManager implements StatusPresentationManagerInterface {

    const DEFAULT_LANG_CODE = 'cs';

    const DEEAULT_HIERARCHY_ROOT_COMPONENT_NAME = 's';

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
     * Pokud nejsou nastaveny hodnoty, nastaví defaultní hodnoty language,  do presentation statusu.
     */
    public function regenerateStatusPresentation(StatusPresentationInterface $statusPresentation): void {
        ## defaultní hodnoty parametrů status presentation

        // předpokládá, že pokud objekt language v statusPresentation byl v session, byl již načten ze session
        // spolu s celým statusPresentation (např. v AppFactory)
        $language = $statusPresentation->getLanguage();
        if (!isset($language)) {
            $language = $this->getRequestedLanguage();
            $statusPresentation->setLanguage($language);
        }

        $menuItem = $statusPresentation->getMenuItem();
        if (!$menuItem) {
            // defaultní node item pro zjištěný jazyk
            $menuItem = $this->getDefaulMenuItem($language->getLangCode());
            $statusPresentation->setMenuItem($menuItem);
        }

        if (!$statusPresentation->getUserActions()) {
            $statusPresentation->setUserActions(new UserActions());  // má default hodnoty
        }
    }

    /**
     * Default LanguageInterface objekt podle kódu jazyka požadovaného v requestu (z hlavičky Accept-Language) apokud takový jazyk aplikace není
     * v databázi, pak podle konstanty třídy DEFAULT_LANG_CODE
     * @return LanguageInterface
     */
    private function getRequestedLanguage() {
        $requestedLangCode = $this->statusPresentation->getRequestedLangCode();
        $language = $this->languageRepo->get($requestedLangCode);
        if (!isset($language)) {
            user_error("Podle hlavičky requestu Accept-Language je požadován kód jazyka $requestedLangCode. "
                    . "Takový kód jazyka nebyl nalezen mezi jazyky aplikace. Nastaven default jazyk aplikace.", E_USER_NOTICE);
            $language = $this->languageRepo->get(self::DEFAULT_LANG_CODE);
        }
        return $language;
    }

    /**
     * Default menu item uid - kořenová položka menu určeného konstantou třídy DEEAULT_HIERARCHY_ROOT_COMPONENT_NAME
     * @return MenuNodeInterface
     */
    private function getDefaulMenuItem($langCode) {
        $uidFk = $this->menuRootRepo->get(self::DEEAULT_HIERARCHY_ROOT_COMPONENT_NAME)->getUidFk();
        return $this->menuItemRepo->get($langCode, $uidFk );    // kořen menu
    }
}
