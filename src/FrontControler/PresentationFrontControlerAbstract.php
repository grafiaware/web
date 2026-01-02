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
        $editorActions = $this->statusSecurityRepo->get()->getEditorActions();
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

    protected function setPresentationMenuItem($menuItem) {
        $statusPresentation = $this->statusPresentationRepo->get();
        $statusPresentation->setMenuItem($menuItem);
    }

    protected function setPresentationStaticItem($staticItem=null) {
        $statusPresentation = $this->statusPresentationRepo->get();
        $statusPresentation->setStaticItem($staticItem);        
    }
    
    protected function getPresentationLangCode() {
        return $this->statusPresentationRepo->get()->getLanguageCode();
    }

    /**
     * Nastaví nebo přenastaví jazyk prezentace.
     * 
     * @param type $languageCode
     * @return type
     */
    protected function setPresentationLangCode($languageCode) {
        return $this->statusPresentationRepo->get()->setLanguageCode($languageCode);
    }

}
