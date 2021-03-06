<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Middleware\Status;

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

use Model\Entity\StatusPresentation;
use Model\Repository\StatusPresentationRepo;
use Model\Repository\{
    LanguageRepo, MenuRootRepo, MenuItemRepo
};
use Model\Entity\{
    HierarchyAggregateInterface, LanguageInterface, EntitySingletonInterface
};

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

        /** @var StatusPresentationRepo $statusPresentationRepo */
        $statusPresentationRepo = $this->container->get(StatusPresentationRepo::class);

        $statusPresentation = $statusPresentationRepo->get();
        if (!isset($statusPresentation)) {
            $statusPresentation = new StatusPresentation();
            $statusPresentationRepo->add($statusPresentation);
        }
        $this->beforeHandle($statusPresentation, $request);

        return $handler->handle($request);
    }

    /**
     * Pokud nejsou nastaveny hodnoty, nastaví defaultní hodnoty language, menuItem do presentation statusu.
     *
     * @param StatusPresentationInterface $statusPresentation
     * @param ServerRequestInterface $request
     * @return void
     */
    public function beforeHandle(StatusPresentation $statusPresentation, ServerRequestInterface $request): void {

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
            $statusPresentation->setLastGetResourcePath($restUri);
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