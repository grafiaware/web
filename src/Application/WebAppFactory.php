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

    /**
     *
     * @return AppInterface
     */
    public function createFromEnvironment(Environment $environment): AppInterface {
        $app = parent::createFromEnvironment($environment);   // mj. automaticky nastaví urlInfo jako atribut requestu s klíčem AppFactory::URL_INFO_ATTRIBUTE_NAME

        // přidá do requestu atribut s jazykem požadovaným hlavičkou Accept-Language
        $request = $app->getServerRequest();
        $requestLocale = $this->getRequestLocale($request);
        $requestLangCode = $this->getRequestLangCode($requestLocale);
        $request = $request->withAttribute(self::REQUESTED_LANGUAGE_ATTRIBUTE_NAME, $requestLangCode);

        $localeLangCode = [
                'cs' => 'cs_CZ',
                'de' => 'de_DE',
                'en' => 'en_US'
            ];
        $appCodeset = "UTF8";  // bez pomlčky! (ne UTF-8)

        $appLocale = $localeLangCode[$requestLangCode].".".$appCodeset;
        $acceptedLocale = setlocale(LC_ALL, $appLocale);

        $app->setServerRequest($request);

        Message::setAppLocale(self::INITIAL_APP_LANGCODE);
        require_once __DIR__.'/Messages/addTranslations.php';

        if (PES_DEVELOPMENT) {
            Message::setLogger(FileLogger::getInstance("Logs/App", 'Messages.log', FileLogger::REWRITE_LOG));

            $logger = FileLogger::getInstance('Logs/App', 'WebAppFactoryLogger.log', FileLogger::REWRITE_LOG);
            $logger->info((new RequestDumper())->dump($request));

            $logger->info("Locale code from http request: '$requestLocale'.");
            $logger->info("Short lang code from http request: '$requestLangCode'.");
            $logger->info("Try to set application locale from http request: '$appLocale'.");
            $logger->info("Accepted locale by setlocale() function: '$acceptedLocale'.");

            $pathLogger = FileLogger::getInstance('Logs/App', 'RequestPathLogger.log', FileLogger::APPEND_TO_LOG);
            $uri = $request->getUri();
            $xPoweredByHeader = $request->getHeaderLine('x-powered-by');
            $refererHeader = $request->getHeaderLine('referer');
            $pathLogger->info((string) $uri);
            if ($xPoweredByHeader) {
                $pathLogger->info("x-powered-by $xPoweredByHeader");
            }
            if ($refererHeader) {
                $pathLogger->info("referer $refererHeader");
            }
        } else {
            Message::setLogger(FileLogger::getInstance("Logs/App", 'Messages.log', FileLogger::REWRITE_LOG));
        }

        return $app;
    }


    private function getRequestLocale(ServerRequestInterface $request) {
        $acceptLanguageHeader = $request->getHeaderLine('Accept-Language');
        // \Locale potřebuje mít poveleno rozšíření intl (php.ini)
        return \Locale::acceptFromHttp($acceptLanguageHeader);  // když chybí hlavička vrací NULL
    }

    private function getRequestLangCode($requestLocale) {
        // \Locale potřebuje mít poveleno rozšíření intl (php.ini)
        return $requestLocale ? \Locale::getPrimaryLanguage($requestLocale) : '';
    }
}
