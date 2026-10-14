<?php

namespace Transformator\Middleware\Transformator;

use Pes\Application\Middleware\AppMiddlewareAbstract;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Site\ConfigurationCache;

use Red\Model\Dao\MenuItemDao;
use Red\Model\Dao\MenuItemDaoInterface;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Entity\PresentationInterface;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Enum\FlashSeverityEnum;

use Pes\Http\Body;
use Pes\Core\Debug\Timer;
use Pes\Model\Dao\StatusDao;

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
        $timer = new Timer(false);
        $timer->start();
        $response = $handler->handle($request);
        if ($request->getMethod()=="GET") {
            $handleTime = $timer->interval();
            $this->container = $this->getApp()->getAppContainer();  // měl by mít nastaven kontejner z middleware (web nebo red)
            $newBody = new Body(fopen('php://temp', 'r+'));
            $newBody->write($this->transform($request, $response->getBody()->getContents()));
            $response = $response->withHeader('X-RED-Handle-Time', sprintf('%2.3fms', ($handleTime) * 1000));
            $response = $response->withHeader(self::HEADER, sprintf('%2.3fms', ($timer->interval()) * 1000));
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
        if (in_array('template substitutions',$replaceConfig)) {
            $replacer->replaceTemplateStrings($request, $text);
        }
        // slots
        if (in_array('slots',$replaceConfig)) {
            $replacer->replaceSlots($text);            
        }
        
        // RS strings
        if (in_array('rs substitutions',$replaceConfig)) {
            $replacer->replaceRSStrings($request, $text);
        }
        // RS urls
        if (in_array('rs list urls',$replaceConfig)) {
            try {
                $key = 'list';
                $dao = $this->container->get(MenuItemDao::class);
                /** @var StatusPresentationRepo $statusPresentationRepo */
                $statusPresentationRepo = $this->container->get(StatusPresentationRepo::class);
                //TODO: $statusPresentation může být null -> exception
                $statusPresentation = $statusPresentationRepo->getClone();      // jen ke čtení
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
        if ($this->isPresentationEditable()) {
            $this->setEditorFlashMessage($e->getMessage());
        }
        if ($this->hasLogger()) {
            $this->getLogger()->notice("Pro uri $requestUri - {$e->getMessage()}");
        }
    }
    
    private function flashAndLogIncorrectHtmlSyntax($requestUri, InvalidHtmlSourceException $e) { 
        if ($this->isPresentationEditable()) {
            $this->setEditorFlashMessage($e->getMessage());
        }
        if ($this->hasLogger()) {
            $this->getLogger()->notice("{$e->getMessage()} Nalezen počáteční řetězec a nenalezen konec pro $requestUri.");
        }
    }

    /**
     * Globální editační režim prezentace ({@see \Red\Model\Entity\EditorActions::presentEditableContent()}),
     * ne item action „tužtička“ u konkrétního paperu.
     */
    private function isPresentationEditable(): bool {
        /** @var StatusSecurityRepo $statusSecurityRepo */
        $statusSecurityRepo = $this->container->get(StatusSecurityRepo::class);
        $editorActions = $statusSecurityRepo->getClone()?->getEditorActions();
        return $editorActions ? $editorActions->presentEditableContent() : false;
    }

    /**
     * Flash pro editora: otevřená session → get(); po UnlockStatus::finish() (cascade)
     * nejdřív StatusDao::reopen(), aby get() nespadlo a FlashStatus mohl flushnout
     * (u paper cascade UnlockStatus sám reopen nedělá).
     */
    private function setEditorFlashMessage(string $message): void {
        /** @var StatusFlashRepo $statusFlashRepo */
        $statusFlashRepo = $this->container->get(StatusFlashRepo::class);
        if ($statusFlashRepo->isFinished()) {
            /** @var StatusDao $statusDao */
            $statusDao = $this->container->get(StatusDao::class);
            $statusDao->reopen();
        }
        $flash = $statusFlashRepo->get();
        if ($flash !== null) {
            $flash->setMessage($message, FlashSeverityEnum::WARNING);
        }
    }
}
