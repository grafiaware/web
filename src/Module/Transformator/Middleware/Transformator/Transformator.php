<?php

namespace Transformator\Middleware\Transformator;

use Pes\Middleware\AppMiddlewareAbstract;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Site\ConfigurationCache;

use Red\Model\Dao\MenuItemDao;
use Red\Model\Dao\MenuItemDaoInterface;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Entity\StatusPresentationInterface;
use Status\Model\Repository\StatusFlashRepo;

use Pes\Http\Body;

use Replace\Replace;
use Replace\Exception\InvalidHtmlSourceException;
use Replace\Exception\ListValueNotFoundInDatabaseException;

/**
 * Description of Transformator
 * Transformuje obsahy stránek uložených v původní verzi rs.
 *
 * @author pes2704
 */
class Transformator extends AppMiddlewareAbstract implements MiddlewareInterface {

    const HEADER = 'X-RED-Transformation-Time';

    /**
     * @var ContainerInterface
     */
    private $container;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

        $response = $handler->handle($request);
        if ($request->getMethod()=="GET") {
            $startTime = microtime(true);
            $this->container = $this->getApp()->getAppContainer();  // měl by mít nastaven kontejner z middleware (web nebo red)
            $newBody = new Body(fopen('php://temp', 'r+'));
            $newBody->write($this->transform($request, $response->getBody()->getContents()));
            $response = $response->withHeader(self::HEADER, sprintf('%2.3fms', (microtime(true) - $startTime) * 1000));
            $response = $response->withBody($newBody);
        }
        return $response;
    }

    /**
     * Zamění v textu předepsané vzory, href odkazy ze starého rs a sloty.
     * Metody replace mohou vyhazovat výjimky, ale vždy upraví text až do místa, kde výjimka vznikla.
     * Zde má přednost zobrazení obsahu uživateli i v prípadě vzniku výjimky - tj. metoda vrací obsah tak, jak se ho podařilo upravit, vytvoří flash message a případně záznam v logu
     * 
     * @param ServerRequestInterface $request
     * @param type $text
     * @return string
     */
    private function transform(ServerRequestInterface $request, $text): string {
        $replaceConfig = ConfigurationCache::transformator()['replace'];
//             => [
//                'template substitutions',
//                'slots',
//                'rs substitutions',
//                'rs list urls'
//            ],        
        /** @var Replace $replacer */
        $replacer = $this->container->get(Replace::class);
        // template strings
        if (array_key_exists('template substitutions',$replaceConfig)) {
            $replacer->replaceTemplateStrings($request, $text);
        }
        // slots
        if (array_key_exists('slots',$replaceConfig)) {
            $replacer->replaceSlots($text);            
        }
        
        // RS strings
        if (array_key_exists('rs substitutions',$replaceConfig)) {
            $replacer->replaceRSStrings($request, $text);
        }
        // RS urls
        if (array_key_exists('rs list urls',$replaceConfig)) {
            try {
                $key = 'list';
                $dao = $this->container->get(MenuItemDao::class);
                /** @var StatusPresentationRepo $statusPresentationRepo */
                $statusPresentationRepo = $this->container->get(StatusPresentationRepo::class);
                //TODO: $statusPresentation může být null -> exception
                $statusPresentation = $statusPresentationRepo->get();
                $replacer->replaceRsUrlsInHref($request, $text, $key, $dao, $statusPresentation);
            } catch (InvalidHtmlSourceException $e) {
                $this->flashAndLogIncorrectHtmlSyntax($request->getUri()->getPath(), $e);
            } catch (ListValueNotFoundInDatabaseException $e) {
                $this->flashAndLogNotFound($request->getUri()->getPath(), $e);
            }
        }
        return $text;
    }
    
    private function flashAndLogNotFound($requestUri, ListValueNotFoundInDatabaseException $e) {
        /** @var StatusFlashRepo $statusFlashRepo */
        $statusFlashRepo = $this->container->get(StatusFlashRepo::class);
        if (PES_DEVELOPMENT) {
            $statusFlashRepo->get()->setMessage($e->getMessage());
        }
        if ($this->hasLogger()) {
            $this->getLogger()->notice("Pro uri $requestUri - {$e->getMessage()}");
        }
    }
    
    private function flashAndLogIncorrectHtmlSyntax($requestUri, InvalidHtmlSourceException $e) {
        /** @var StatusFlashRepo $statusFlashRepo */
        $statusFlashRepo = $this->container->get(StatusFlashRepo::class);        
        if (PES_DEVELOPMENT) {
            $statusFlashRepo->get()->setMessage($e->getMessage());
        }
        if ($this->hasLogger()) {
            $this->getLogger()->notice("{$e->getMessage()} Nalezen počáteční řetězec a nenalezen konec pro $requestUri.");
        }
    }
}