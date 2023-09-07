<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Web\Middleware\Page;
use Status\Model\Repository\StatusPresentationRepo;
use Red\Model\Repository\LanguageRepo;
use Site\ConfigurationCache;
/**
 * Description of Prepare
 *
 * @author pes2704
 */
class Prepare {
    public static function preperePresentation(StatusPresentationRepo $statusPresentationRepo, LanguageRepo $languageRepo) {
        $statusPresentation = $statusPresentationRepo->get();
        if (!$statusPresentation->getLanguage()) {
            $requestedLangCode = $statusPresentation->getRequestedLangCode();
            $language = $languageRepo->get($requestedLangCode);
            if (!isset($language)) {
                user_error("Podle hlavičky requestu Accept-Language je požadován kód jazyka $requestedLangCode. "
                        . "Takový kód jazyka nebyl nalezen mezi jazyky v databázi. Bude nastaven default jazyk aplikace.", E_USER_NOTICE);
                $defaultLangCode = ConfigurationCache::presentationStatus()['default_lang_code'];
                $language = $languageRepo->get($defaultLangCode);
                if (!isset($language)) {
                    throw new UnexpectedValueException("Kód jazyka nastavený v konfiguraci jako výchozí jazyk nebyl nalezen mezi jazyky v databázi.");
                }
            }
            $statusPresentation->setLanguage($language);
        }
    }
}
