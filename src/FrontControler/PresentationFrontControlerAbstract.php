<?php

namespace FrontControler;
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Access\AccessPresentationInterface;

use \Pes\Router\Resource\ResourceRegistryInterface;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Application\WebAppFactory;

/**
 * Description of PresentationFrontControlerAbstract
 *
 * @author pes2704
 */
abstract class PresentationFrontControlerAbstract extends FrontControlerAbstract implements PresentationFrontControlerInterface {

    /**
     * @var ResourceRegistryInterface
     */
    protected $resourceRegistry;
    
    /**
     * 
     * @var AccessPresentationInterface
     */
    protected $accessPresentation;


    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            AccessPresentationInterface $accessPresentation
            ) {
            parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
            $this->accessPresentation = $accessPresentation;
    }    
    
    ### headers ###
    
    /**
     * Přetěžuje addCacheHeaders() z FrontControlerAbstract
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    protected function addCacheHeaders(ResponseInterface $response): ResponseInterface {
        $editorActions = $this->statusSecurityRepo->getClone()->getEditorActions();
        if ($editorActions AND $editorActions->presentEditableContent()) {
            $response = $response->withHeader('Cache-Control', 'no-store, no-cache');
        } else {
            $response = $response->withHeader('Cache-Control', 'no-store, no-cache');
//            $response = $response->withHeader('Cache-Control', 'public, max-age=0');  
        }
        $cls = (new \ReflectionClass($this))->getShortName();
        return $response->withHeader('X-RED-Controlled', "$cls");
    }   
    
    ### status control methods ###

    /**
     * Nastaví prezentovaný menu item.
     * Po StatusDao::finish() (cascade) mutuje mutable clone v paměti; zápis do session až po UnlockStatus::reopen() + PresentationStatus flush.
     */
    protected function setPresentationMenuItem($menuItem) {
        $this->mutatePresentationStatus(function ($statusPresentation) use ($menuItem) {
            $statusPresentation->setMenuItem($menuItem);
        });
    }

    /**
     * Nastaví prezentovaný static item.
     * Po StatusDao::finish() (cascade) mutuje mutable clone v paměti; zápis do session až po UnlockStatus::reopen() + PresentationStatus flush.
     */
    protected function setPresentationStaticItem($staticItem=null) {
        $this->mutatePresentationStatus(function ($statusPresentation) use ($staticItem) {
            $statusPresentation->setStaticItem($staticItem);
        });
    }
    
    protected function getPresentationLangCode() {
        return $this->statusPresentationRepo->getClone()->getLanguageCode();   // vrací klon i když je session close - klon je immutable 
    }

    /**
     * Nastaví nebo přenastaví jazyk prezentace.
     * 
     * @param type $languageCode
     * @return type
     */
    protected function setPresentationLangCode($languageCode) {
        return $this->statusPresentationRepo->get()->setLanguageCode($languageCode); // nesmí být session close
    }

    /**
     * Mutace Presentation statusu: při otevřené session přes get(), po finish() přes getClone(false) + replaceEntityInMemory.
     *
     * @param callable $mutator function(PresentationInterface $statusPresentation): void
     */
    private function mutatePresentationStatus(callable $mutator): void {
        if ($this->statusPresentationRepo->isFinished()) {
            $statusPresentation = $this->statusPresentationRepo->getClone(false);
            $mutator($statusPresentation);
            $this->statusPresentationRepo->replaceEntityInMemory($statusPresentation);
            return;
        }
        $statusPresentation = $this->statusPresentationRepo->get();
        $mutator($statusPresentation);
        // Po early flush v PresentationStatus je loadedFragment unset — zajistit pozdější flush / reopen path.
        $this->statusPresentationRepo->replaceEntityInMemory($statusPresentation);
    }

}
