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
        $this->presetPresentationStatus($statusPresentation, $request);
        $response = $handler->handle($request);
        return $response;
    }

    private function getOrCreateStatusIfNotExists() {
        /** @var StatusPresentationRepo $statusPresentationRepo */
        $statusPresentationRepo = $this->container->get(StatusPresentationRepo::class);

        $statusPresentation = $statusPresentationRepo->get();
        if (!isset($statusPresentation)) {
            $statusPresentation = $this->container->get(StatusPresentation::class);
            $statusPresentationRepo->add($statusPresentation);
        }
        return $statusPresentation;
    }

    /**
     * Nastaví jazyk prezentace pokud není nastaven. Pokud je již se statusu entita language, požadovaný jazyk v requestu nehraje roli.
     *
     * @param type $statusPresentation
     */
    private function presetPresentationStatus(StatusPresentation $statusPresentation, ServerRequestInterface $request) {
        // jazyk prezentace
        if (is_null($statusPresentation->getLanguage())) {
            $langCode = $this->getRequestedLangCode($request);
            $statusPresentation->setRequestedLangCode($langCode);
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
            user_error("Podle hlavičky requestu Accept-Language je požadován kód jazyka $requestedLangCode. "
                    . "Takový kód jazyka nebyl nalezen mezi jazyky v konfiguraci. Nastaven default jazyk aplikace.", E_USER_NOTICE);
            $langCode = ConfigurationCache::presentationStatus()['default_lang_code'];
        }
        return $langCode;
    }
}