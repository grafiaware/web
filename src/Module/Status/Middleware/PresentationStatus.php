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
use Red\Model\Entity\EditorActions;

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
        //TODO: POST version
        // možná není potřeba ukládat - nebude fungoba seeLastGet
        
        $statusPresentation = $this->createStatusBeforeHandle($request);
        $response = $handler->handle($request);
        $this->setStatusAfterHandle($statusPresentation, $request);        
        $response = $this->addResponseHeaders($response);
        return $response;
    }

    /**
     * Nastaví jazyk prezentace pokud není nastaven. Pokud je již se statusu entita language, požadovaný jazyk v requestu nehraje roli.
     * 
     * @param ServerRequestInterface $request
     */
    private function createStatusBeforeHandle(ServerRequestInterface $request) {
        /** @var StatusPresentationRepo $statusPresentationRepo */
        $this->statusPresentationRepo = $this->container->get(StatusPresentationRepo::class);

        $statusPresentation = $this->statusPresentationRepo->get();
        if (!isset($statusPresentation)) {
            $statusPresentation = new StatusPresentation();
            $this->statusPresentationRepo->add($statusPresentation);
        }        
        $statusPresentation->addUri($this->getRestUri($request));
        if(!$statusPresentation->getMenuItem()) {
            $stop = true;
        }
        // jazyk prezentace
        if (is_null($statusPresentation->getLanguage())) {
            $langCode = $this->getRequestedLangCode($request);
            /** @var LanguageRepo $lanuageRepo */
            $lanuageRepo = $this->container->get(LanguageRepo::class);
            $language = $lanuageRepo->get($langCode);
            $statusPresentation->setRequestedLangCode($langCode);
            $statusPresentation->setLanguage($language);
        }
        return $statusPresentation;
    }

    /**
     * Pro GET request uloží uri do StatusPresentation. 
     * - Neukládá uri pokud request obsahuje hlavičku "X-Cascade", to je využito při kaskádním načítání, 
     *   kdy se neuládají adresy GET requestů, kterými jsou načítány vložené komponenty stránky. 
     * 
     * Poznámka: Použito pro přesměrování redirectLastGet a pro Transform!
     * 
     * @param type $statusPresentation
     * @param type $request
     */
    private function setStatusAfterHandle(StatusPresentationInterface $statusPresentation, $request) {
        if(!$statusPresentation->getMenuItem()) {
            $uri = $this->getRestUri($request);
        }
        if ($request->getMethod()=='GET') {
            if (!$request->hasHeader("X-Cascade")) {
                $statusPresentation->setLastGetResourcePath($this->getRestUri($request));
            }
        }
    }
    
    private function getRestUri(ServerRequestInterface $request) {
        /** @var UriInfoInterface $uriInfo */
        $uriInfo = $request->getAttribute(WebAppFactory::URI_INFO_ATTRIBUTE_NAME);
        return $uriInfo->getRestUri();    
    }
    
    /**
     * Vrací kód jazyka podle kódu jazyka požadovaného v requestu (z hlavičky Accept-Language), pokud takový jazyk existuje v seznamu akceptovaných jazyků v konfiguraci. 
     * Pokud takový jazyk aplikace není v konfiguraci, pak vrací defaultní hodnotu jazyka aplikace, také uvedenou v konfiguraci.
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