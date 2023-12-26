<?php
namespace Web\Middleware\Page\PrepareService;

use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;
use Red\Model\Repository\LanguageRepo;
use Red\Service\ItemAction\ItemActionServiceInterface;
use Site\ConfigurationCache;

/**
 * Description of Prepare
 *
 * @author pes2704
 */
class Prepare implements PrepareStatusServiceInterface {
    
    private $statusPresentationRepo;
    private $languageRepo;
    private $statusSecurityRepo;
    private $itemActionService;

    public function __construct(
            StatusPresentationRepo $statusPresentationRepo, 
            LanguageRepo $languageRepo,
            StatusSecurityRepo $statusSecurityRepo,
            ItemActionServiceInterface $itemActionService
            ) {
                $this->statusPresentationRepo = $statusPresentationRepo;
                $this->languageRepo = $languageRepo;
                $this->statusSecurityRepo = $statusSecurityRepo;
                $this->itemActionService = $itemActionService;
    }
    
    /**
     * Provede akce závislé prezentačním a security statusu, které potřebují přístup k databázi webu. 
     * Tyto jsou spuštěny údálostmi v Status middleware, ale je nažádoucí je provádět v StatusP middleware, protože by to vyžadovalo přístup k databázi již v tomto middleware.
     * 
     * StatusPresentation akce:
     * Metoda nastaví jazyk prezentace podle jazyka požadovaného v requestu (požadovaný jazyk byl nastaven do status presentation v status presentation middleware) 
     * a podle dostupných jazykových verzí v databázi (potřebuje LanguageRepo).
     * 
     * StatusSecurity akce:
     * Metoda smaže v databázi akce ItemActions toho uživatele, který se v předcháuejícím (POST) requestu odhlásil.
     * 
     * @param StatusPresentationRepo $statusPresentationRepo
     * @param LanguageRepo $languageRepo
     * @throws UnexpectedValueException
     */
    public function prepareDbByStatus() {
        $statusPresentation = $this->statusPresentationRepo->get();
        if (!$statusPresentation->getLanguage()) {
            $requestedLangCode = $statusPresentation->getRequestedLangCode();
            $language = $this->languageRepo->get($requestedLangCode);
            if (!isset($language)) {
                user_error("Podle hlavičky requestu Accept-Language je požadován kód jazyka $requestedLangCode. "
                        . "Takový kód jazyka nebyl nalezen mezi jazyky v databázi. Bude nastaven default jazyk aplikace.", E_USER_NOTICE);
                $defaultLangCode = ConfigurationCache::presentationStatus()['default_lang_code'];
                $language = $this->languageRepo->get($defaultLangCode);
                if (!isset($language)) {
                    throw new UnexpectedValueException("Kód jazyka nastavený v konfiguraci jako výchozí jazyk nebyl nalezen mezi jazyky v databázi.");
                }
            }
            $statusPresentation->setLanguage($language);
        }
        $userActions = $this->statusSecurityRepo->get()->getUserActions();
        if(isset($userActions)) {
            $loggedOffUser = $this->statusSecurityRepo->get()->getUserActions()->lastLoggedOffUsername();  // logged off user je automaticky smazáno z UserActions  
        }
        if (isset($loggedOffUser)) {
            // skutečné smazámí proběhne až po skončení skriptu - jde o GET request a tak zpoždění není velké (neprovádím repo->flush())
            $this->itemActionService->removeUserItemActions($loggedOffUser);  // skutečné smazámí proběhne až po skončení skriptu
        }
    }
}
