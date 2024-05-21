<?php

namespace FrontControler;

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
    
    ### headers ###

    /**
     * Přetěžuje addHeaders() z FrontControlerAbstract
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function addHeaders(ResponseInterface $response): ResponseInterface {
        $userActions = $this->statusSecurityRepo->get()->getUserActions();
        if ($userActions AND $userActions->presentEditableContent()) {
            $response = $response->withHeader('Cache-Control', 'no-cache');
        } else {
            $response = $response->withHeader('Cache-Control', 'public, max-age=0');  
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
    public function createResponseFromString($stringContent): ResponseInterface {

        $response = parent::createResponseFromString($stringContent);
        
//        $statusPresentation = $this->statusPresentationRepo->get();
//        if ($request->getMethod()=='GET') {
//            /** @var UriInfoInterface $uriInfo */
//            $uriInfo = $request->getAttribute(WebAppFactory::URI_INFO_ATTRIBUTE_NAME);
//            if (!$request->hasHeader("X-Cascade")) {
//                $restUri = $uriInfo->getRestUri();
//                $statusPresentation->setLastGetResourcePath($restUri);
//            }
//        }
        return $response;
    }

    ### status control methods ###

    protected function setPresentationMenuItem($menuItem) {
        $statusPresentation = $this->statusPresentationRepo->get();
        $statusPresentation->setMenuItem($menuItem);
    }

    protected function getPresentationLangCode() {
        return $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
    }



}
