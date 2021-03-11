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

use Pes\Application\UriInfoInterface;

use Model\Repository\{
    LanguageRepo, MenuRootRepo, MenuItemRepo
};

use Model\Entity\{
    HierarchyAggregateInterface, LanguageInterface, EntitySingletonInterface
};

use Model\Entity\{
    StatusPresentation
};


/**
 * Description of PresentationController
 *
 * @author pes2704
 */
class StatusPresentationManager implements StatusManagerInterface {

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
    public function createStatus(): EntitySingletonInterface {
        return new StatusPresentation();
    }

    /**
     * Pokud nejsou nastaveny hodnoty, nastaví defaultní hodnoty language, menuItem do presentation statusu.
     *
     * @param StatusPresentationInterface $statusPresentation
     * @param ServerRequestInterface $request
     * @return void
     */
    public function renewStatus(EntitySingletonInterface $statusPresentation, ServerRequestInterface $request): void {

        ## defaultní hodnoty parametrů status presentation
        // jazyk prezentace
        $language = $statusPresentation->getLanguage();
        if (!isset($language)) {
            $language = $this->getRequestedLanguage($request);
            $statusPresentation->setLanguage($language);
        }
        if ($request->getMethod()=='GET') {

            /** @var UriInfoInterface $uriInfo */
            $uriInfo = $request->getAttribute(WebAppFactory::URI_INFO_ATTRIBUTE_NAME);
            $restUri = $uriInfo->getRestUri();
            if (strpos($restUri, "www/last") === false ) {
                $statusPresentation->setLastGetResourcePath($restUri);
            }
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
}
