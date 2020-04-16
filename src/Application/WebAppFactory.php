<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Http\Environment;
use Pes\Application\AppFactory;
use Pes\Application\AppInterface;

use Pes\Container\AutowiringContainer;
use Pes\Container\Container;
use Pes\Text\Message;
use Pes\Logger\FileLogger;
use Pes\Http\Helper\RequestDumper;

use Container\AppContainerConfigurator;
use Model\Repository\StatusPresentationRepo;
use Model\Entity\StatusPresentation;
use Model\Entity\StatusPresentationInterface;

/**
 * Description of WebAppFactory
 *
 * @author pes2704
 */
class WebAppFactory extends AppFactory {

    const INITIAL_APP_LANGCODE = 'cs';

    public function __construct() {
        parent::__construct((new AppContainerConfigurator())->configure(new Container()));
//        parent::__construct((new AppContainerConfigurator())->configure(new Container(new AutowiringContainer())));
    }

    /**
     *
     * @return AppInterface
     */
    public function createFromEnvironment(Environment $environment): AppInterface {
        $app = parent::createFromEnvironment($environment);   // mj. automaticky nastaví urlInfo jako atribut requestu s klíčem AppFactory::URL_INFO_ATTRIBUTE_NAME

        if (PES_DEVELOPMENT) {
            Message::setLogger(FileLogger::getInstance("Logs/App", 'Messages.log', FileLogger::REWRITE_LOG));

            $request = $app->getServerRequest();
            $logger = FileLogger::getInstance('Logs/App', 'AppLogger.log', FileLogger::REWRITE_LOG);
            $logger->info((new RequestDumper())->dump($request));
            $pathLogger = FileLogger::getInstance('Logs/App', 'RequestPathLogger.log', FileLogger::APPEND_TO_LOG);
            $uri = $request->getUri();
            $pathLogger->info((string) $uri);

        } else {
            Message::setLogger(FileLogger::getInstance("Logs/App", 'Messages.log', FileLogger::REWRITE_LOG));
        }

        $statusPresentation = $this->regeneratePresentationStatus($request);

        //messages podle jazyka prezentace
        $language = $statusPresentation->getLanguage();
        $localeShort = $language ? $language->getLangCode() : self::INITIAL_APP_LANGCODE;
        Message::setAppLocale($localeShort);
        require_once __DIR__.'/Messages/addTranslations.php';
        return $app;
    }

    /**
     * Obnoví statusPresentation ze session, pokud není vytvoří nový.
     * Obnovuje se již zde, protože je třeba v statusPresentation nastavit jazyk požadovaný hlavičkou Accept-Language
     */
    private function regeneratePresentationStatus(ServerRequestInterface $request) {
        /* @var $statusPresentationRepo StatusPresentationRepo */
        $statusPresentationRepo = $this->appContainer->get(StatusPresentationRepo::class);
        /* @var $statusPresentation StatusPresentationInterface */
        $statusPresentation = $statusPresentationRepo->get();
        if ( !isset($statusPresentation)) {
             $statusPresentation = new StatusPresentation();
             $statusPresentationRepo->add($statusPresentation);
        }
        $statusPresentation->setRequestedLangCode($this->getLanguage($request));
        return $statusPresentation;
    }

    /**
     * Default langCode podle konstanty třídy DEFAULT_LANG_CODE
     * @return string
     */
    private function getLanguage(ServerRequestInterface $request) {
        $acceptLanguageHeader = $request->getHeaderLine('Accept-Language');
        // \Locale potřebuje mít poveleno rozšíření intl (php.ini)
        $requestLocale = \Locale::acceptFromHttp($acceptLanguageHeader);  // když chybí hlavička vrací NULL
        $langCode = $requestLocale ? \Locale::getPrimaryLanguage($requestLocale) : '';
        return $langCode;
    }
}
