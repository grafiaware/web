<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace StatusModel;

use Model\Repository\StatusPresentationRepo;
use Model\Repository\StatusFlashRepo;
use Model\Repository\LanguageRepo;
use Model\Repository\MenuRepo;
use Model\Repository\MenuRootRepo;

use Model\Entity\StatusPresentation;

use Model\Entity\{
    MenuNodeInterface, StatusPresentationInterface, StatusFlashInterface, LanguageInterface
};
use Model\Entity\UserActions;

/**
 * Description of PresentationController
 *
 * @author pes2704
 */
class StatusPresentationModel implements StatusPresentationModelInterface {

    const DEFAULT_LANG_CODE = 'cs';

    const DEEAULT_HIERARCHY_ROOT_COMPONENT_NAME = 's';

    /**
     * @var StatusPresentationInterface
     */
    protected $statusPresentationRepo;

    /**
     * @var StatusFlashRepo
     */
    protected $statusFlashRepo;

    /**
     * @var LanguageRepo
     */
    protected $languageRepo;

    /**
     * @var StatusPresentation
     */
    private $statusPresentation;

    /**
     * @var MenuRepo
     */
    protected $menuRepo;

    /**
     * @var MenuRootRepo
     */
    protected $menuRootRepo;

    /**
     * @var MenuItemRepo
     */
    protected $menuItemRepo;

    /**
     * @var bool
     */
    private $isRegenerated = false;


    public function __construct(
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo,
            LanguageRepo $languageRepo,
            MenuRepo $menuRepo,
            MenuRootRepo $menuRootRepo,
            MenuItemRepo $menuItemRepo
            ) {
        $this->statusPresentationRepo = $statusPresentationRepo;
        $this->statusFlashRepo = $statusFlashRepo;
        $this->languageRepo = $languageRepo;
        $this->menuRepo = $menuRepo;
        $this->menuRootRepo = $menuRootRepo;
        $this->menuItemRepo = $menuItemRepo;

    }

    /**
     * Pokud nejsou nastaveny hodnoty, nastaví defaultní hodnoty language,  do presentation statusu.
     */
    private function regenerateStatusPresentation(): void {
        $this->statusPresentation = $this->statusPresentationRepo->get();

        ## defaultní hodnoty parametrů status presentation

        // předpokládá, že pokud objekt language v statusPresentation byl v session, byl již načten ze session
        // spolu s celým statusPresentation (např. v AppFactory)
        $language = $this->statusPresentation->getLanguage();
        if (!isset($language)) {
            $language = $this->getRequestedLanguage();
            $this->statusPresentation->setLanguage($language);
        }

        $menuItem = $this->statusPresentation->getMenuItem();
        if (!$menuItem) {
            // defaultní node item pro zjištěný jazyk
            $menuItem = $this->getDefaulMenuItem($language->getLangCode());
            $this->statusPresentation->setMenuItem($menuItem);
        }

        if (!$this->statusPresentation->getUserActions()) {
            $this->statusPresentation->setUserActions(new UserActions());  // má default hodnoty
        }

        $this->isRegenerated = true;
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

    /**
     *
     * @return StatusPresentationInterface
     */
    public function getStatusPresentation($a=null) {
        assert(!isset($a), "Volání getStatusPresentation s parametrem!!");
        if (!$this->isRegenerated) {
            $this->regenerateStatusPresentation();
        }
        return $this->statusPresentationRepo->get();
    }

    /**
     *
     * @return StatusFlashInterface
     */
    public function getStatusFlash() {
        return $this->statusFlashRepo->get();
    }
}
