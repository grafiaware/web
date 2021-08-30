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
use Pes\Application\UriInfoInterface;

use Application\WebAppFactory;
use Site\Configuration;

use Pes\Container\Container;
use Container\DbUpgradeContainerConfigurator;
use Container\HierarchyContainerConfigurator;

use Status\Model\Entity\StatusPresentation;
use Status\Model\Repository\StatusPresentationRepo;
use Red\Model\Repository\LanguageRepo;

use Red\Model\Entity\LanguageInterface;

use UnexpectedValueException;

/**
 * Description of Status
 *
 * @author pes2704
 */
class PresentationStatus extends AppMiddlewareAbstract implements MiddlewareInterface {

    private $container;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        // potřebuje novou databázi (language, menuItem a menuRoot) -> HierarchyContainerConfigurator a DbUpgradeContainerConfigurator
        $this->container =
                (new HierarchyContainerConfigurator())->configure(
                    (new DbUpgradeContainerConfigurator())->configure(
                        (new Container($this->getApp()->getAppContainer())) //->addContainerInfo("PresentationStatus")
                    )
                );

        $statusPresentation = $this->createStatusIfNotExists();
        $this->presetPresentationLanguage($statusPresentation);
        $this->presetLastGetResourcePath($statusPresentation, $request);
        return $handler->handle($request);
    }

    private function createStatusIfNotExists() {
        /** @var StatusPresentationRepo $statusPresentationRepo */
        $statusPresentationRepo = $this->container->get(StatusPresentationRepo::class);

        $statusPresentation = $statusPresentationRepo->get();
        if (!isset($statusPresentation)) {
            $statusPresentation = new StatusPresentation();
            $statusPresentationRepo->add($statusPresentation);
        }
        return $statusPresentation;
    }

    /**
     * Pokud nejsou nastaveny hodnoty, nastaví defaultní hodnoty language, menuItem do presentation statusu.
     *
     * @param StatusPresentationInterface $statusPresentation
     * @param ServerRequestInterface $request
     * @return void
     */
    private function presetLastGetResourcePath(StatusPresentation $statusPresentation, ServerRequestInterface $request): void {
        if ($request->getMethod()=='GET') {
            /** @var UriInfoInterface $uriInfo */
            $uriInfo = $request->getAttribute(WebAppFactory::URI_INFO_ATTRIBUTE_NAME);
            $restUri = $uriInfo->getRestUri();
            $statusPresentation->setLastGetResourcePath($restUri);
        }
    }

    /**
     * Nastaví jazyk prezentace pokud není nastaven.
     *
     * @param type $statusPresentation
     */
    private function presetPresentationLanguage($statusPresentation) {
        // jazyk prezentace
        $language = $statusPresentation->getLanguage();
        if (!isset($language)) {
            $language = $this->getRequestedLanguage($request);
            $statusPresentation->setLanguage($language);
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
    private function getRequestedLanguage(ServerRequestInterface $request): LanguageInterface {
        $requestedLangCode = $request->getAttribute(WebAppFactory::REQUESTED_LANGUAGE_ATTRIBUTE_NAME);
//TODO:      languageRepo z kontejneru - výkonostní problém - ? nestačí jen jazyk z konfigurace - nebude to celá entita language, alespoň udělej nový menší kontejner místo Hierarchy
        $languageRepo = $this->container->get(LanguageRepo::class);
        $language = $languageRepo->get($requestedLangCode);
        if (!isset($language)) {
            user_error("Podle hlavičky requestu Accept-Language je požadován kód jazyka $requestedLangCode. "
                    . "Takový kód jazyka nebyl nalezen mezi jazyky v databázi. Nastaven default jazyk aplikace.", E_USER_NOTICE);
            $language = $languageRepo->get(Configuration::statusPresentationManager()['default_lang_code']);
            if (!isset($language)) {
                throw new UnexpectedValueException("Kód jazyka nastavený v konfiguraci jako výchozí jazyk nebyl nalezen mezi jazyky v databázi.");
            }
        }
        return $language;
    }
}