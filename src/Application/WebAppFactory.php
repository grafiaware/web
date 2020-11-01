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
    const REQUESTED_LANGUAGE_ATTRIBUTE_NAME = 'requestedLanguage';

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

        // přidá do requestu atribut s jazykem požadovaným hlavičkou Accept-Language
        $request = $app->getServerRequest();
        $request = $request->withAttribute(self::REQUESTED_LANGUAGE_ATTRIBUTE_NAME, $this->getAcceptedLangCode($request));
        $app->setServerRequest($request);

        Message::setAppLocale(self::INITIAL_APP_LANGCODE);
        require_once __DIR__.'/Messages/addTranslations.php';

        if (PES_DEVELOPMENT) {
            Message::setLogger(FileLogger::getInstance("Logs/App", 'Messages.log', FileLogger::REWRITE_LOG));

            $logger = FileLogger::getInstance('Logs/App', 'WebAppFactoryLogger.log', FileLogger::REWRITE_LOG);
            $logger->info((new RequestDumper())->dump($request));
            $pathLogger = FileLogger::getInstance('Logs/App', 'RequestPathLogger.log', FileLogger::APPEND_TO_LOG);
            $uri = $request->getUri();
            $pathLogger->info((string) $uri);

        } else {
            Message::setLogger(FileLogger::getInstance("Logs/App", 'Messages.log', FileLogger::REWRITE_LOG));
        }

        return $app;
    }

    /**
     * Default langCode podle konstanty třídy DEFAULT_LANG_CODE
     * @return string
     */
    private function getAcceptedLangCode(ServerRequestInterface $request) {
        $acceptLanguageHeader = $request->getHeaderLine('Accept-Language');
        // \Locale potřebuje mít poveleno rozšíření intl (php.ini)
        $requestLocale = \Locale::acceptFromHttp($acceptLanguageHeader);  // když chybí hlavička vrací NULL
        $langCode = $requestLocale ? \Locale::getPrimaryLanguage($requestLocale) : '';
        return $langCode;
    }
}
