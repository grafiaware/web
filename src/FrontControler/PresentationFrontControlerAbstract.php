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
     * Přetěžuje addHeaders() z FrontControlerAbstract
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

    ### response ###

    /**
     * Volá rodičovskou metodu Front kontroleru a k vrácenému response přidává řízení cache
     * a ukládá poslední GET request pro "post redirect get".
     *
     * @param ServerRequestInterface $request
     * @param type $stringContent
     * @return ResponseInterface
     */
//    protected function createResponseFromString($stringContent, $status = StatusEnum::_200_OK): ResponseInterface {
//        $response = parent::createResponseFromString($stringContent, $status);
        
//        $statusPresentation = $this->statusPresentationRepo->get();
//        if ($request->getMethod()=='GET') {
//            /** @var UriInfoInterface $uriInfo */
//            $uriInfo = $request->getAttribute(WebAppFactory::URI_INFO_ATTRIBUTE_NAME);
//            if (!$request->hasHeader("X-Cascade")) {
//                $restUri = $uriInfo->getRestUri();
//                $statusPresentation->setLastGetResourcePath($restUri);
//            }
//        }
//        return $response;
//    }

    ### status control methods ###

    protected function setPresentationMenuItem($menuItem) {
        $statusPresentation = $this->statusPresentationRepo->get();
        $statusPresentation->setMenuItem($menuItem);
    }

    protected function getPresentationLangCode() {
        return $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
    }



}
