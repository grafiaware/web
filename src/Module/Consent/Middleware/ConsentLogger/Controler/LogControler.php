<?php
namespace Consent\Middleware\ConsentLogger\Controler;

use Psr\Http\Message\ServerRequestInterface;
use Pes\Http\Request\RequestParams;
use Psr\Http\Message\ResponseInterface;
use FrontControler\FrontControlerAbstract;
use Pes\Logger\FileLogger;

/**
 * Description of LogControler
 *
 * @author pes2704
 */
class LogControler extends FrontControlerAbstract {
    public function logConsent(ServerRequestInterface $request): ResponseInterface {
        $bodyContent = $request->getBody()->getContents();
        $requestParams = new RequestParams();
        $revison = $requestParams->getParsedBodyParam($request, 'revision', false);        
        $consentId = $requestParams->getParsedBodyParam($request, 'consentId', false);        
        $consentTimestamp = $requestParams->getParsedBodyParam($request, 'consentTimestamp', false);        
        $lastConsentTimestamp = $requestParams->getParsedBodyParam($request, 'lastConsentTimestamp', false);
        $consentLogger = $this->container->get('ConsentLogger');
//        $consentLogger = FileLogger::getInstance('PersistentLogs', 'ConsentLogger.log', FileLogger::APPEND_TO_LOG);
        $consentLogger->info("$revison|$consentTimestamp|$lastConsentTimestamp|$consentId|$bodyContent");
        return $this->createJsonPostCreatedResponse([]);
    }
}
