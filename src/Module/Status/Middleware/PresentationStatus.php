<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Status\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use Pes\Middleware\AppMiddlewareAbstract;

use Application\WebAppFactory;
use Site\ConfigurationCache;

use Pes\Container\Container;
use Container\DbUpgradeContainerConfigurator;
use Container\RedModelContainerConfigurator;
use Container\PresentationStatusComfigurator;

use Status\Model\Entity\StatusPresentation;
use Status\Model\Repository\StatusPresentationRepo;
use Red\Model\Repository\LanguageRepo;
use Status\Model\Entity\StatusPresentationInterface;
use Red\Model\Entity\LanguageInterface;
use Red\Model\Entity\UserActions;

use UnexpectedValueException;

/**
 * Description of Status
 *
 * @author pes2704
 */
class PresentationStatus extends AppMiddlewareAbstract implements MiddlewareInterface {

    private $container;
    private $statusPresentationRepo;
    
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

        $this->container =
                (new PresentationStatusComfigurator())->configure(
                    (new RedModelContainerConfigurator())->configure(
                        (new DbUpgradeContainerConfigurator())->configure(
                                new Container($this->getApp()->getAppContainer())
                        )
                )
            );
        $statusPresentation = $this->getOrCreateStatusIfNotExists();
        //TODO: POST version
        // možná není potřeba ukládat - nebude fungoba seeLastGet
        $this->presetPresentationStatus($statusPresentation, $request);
        $response = $handler->handle($request);
        $this->saveLastGetResourcePath($statusPresentation, $request);        
        $response = $this->addResponseHeaders($response);
        return $response;
    }
    
    private function getOrCreateStatusIfNotExists() {
        /** @var StatusPresentationRepo $statusPresentationRepo */
        $this->statusPresentationRepo = $this->container->get(StatusPresentationRepo::class);

        $statusPresentation = $this->statusPresentationRepo->get();
        if (!isset($statusPresentation)) {
            $statusPresentation = $this->container->get(StatusPresentation::class);
            $this->statusPresentationRepo->add($statusPresentation);
        }
        return $statusPresentation;
    }

    /**
     * Nastaví jazyk prezentace pokud není nastaven. Pokud je již se statusu entita language, požadovaný jazyk v requestu nehraje roli.
     *
     * @param type $statusPresentation
     */
    private function presetPresentationStatus(StatusPresentationInterface $statusPresentation, ServerRequestInterface $request) {
        // jazyk prezentace
        if (is_null($statusPresentation->getLanguage())) {
            $langCode = $this->getRequestedLangCode($request);
            /** @var LanguageRepo $lanuageRepo */
            $lanuageRepo = $this->container->get(LanguageRepo::class);
            $language = $lanuageRepo->get($langCode);
            $statusPresentation->setRequestedLangCode($langCode);
            $statusPresentation->setLanguage($language);
        }
    }

    /**
     * Pro GET request uloží uri do StatusPresentation. 
     * - Neukládá uri pokud request obsahuje hlavičku "X-Cascade", to je využito při kaskádním načítání, 
     *   kdy adresy GET requestů, kterými jsou načítání vložené komponenty stránky se neuládají. 
     * 
     * Poznámka: Použito pro přesměrování redirectLastGet a pro Transform!
     * 
     * @param type $statusPresentation
     * @param type $request
     */
    private function saveLastGetResourcePath(StatusPresentationInterface $statusPresentation, $request) {
        if ($request->getMethod()=='GET') {
            /** @var UriInfoInterface $uriInfo */
            $uriInfo = $request->getAttribute(WebAppFactory::URI_INFO_ATTRIBUTE_NAME);
            if (!$request->hasHeader("X-Cascade")) {
                $restUri = $uriInfo->getRestUri();
                $statusPresentation->setLastGetResourcePath($restUri);
            }
        }
    }
    
    /**
     * Default LanguageInterface objekt podle kódu jazyka požadovaného v requestu (z hlavičky Accept-Language) apokud takový jazyk aplikace není
     * v databázi, pak podle konstanty třídy DEFAULT_LANG_CODE
     *
     * @param ServerRequestInterface $request
     * @return LanguageInterface
     * @throws UnexpectedValueException
     */
    private function getRequestedLangCode(ServerRequestInterface $request) {
        $requestedLangCode = $request->getAttribute(WebAppFactory::REQUESTED_LANGUAGE_ATTRIBUTE_NAME);
        
        if (in_array($requestedLangCode, ConfigurationCache::presentationStatus()['accepted_languages'])) {
            $langCode = $requestedLangCode;
        } else {
//            user_error("Podle hlavičky requestu Accept-Language je požadován kód jazyka $requestedLangCode. "
//                    . "Takový kód jazyka nebyl nalezen mezi jazyky v konfiguraci. Nastaven default jazyk aplikace.", E_USER_NOTICE);
            $langCode = ConfigurationCache::presentationStatus()['default_lang_code'];
        }
        return $langCode;
    }
    
    private function addResponseHeaders(ResponseInterface $response) {
        $language = $this->statusPresentationRepo->get()->getLanguage();
        return $response->withHeader('Content-Language', $language->getLocale());
    }    
}