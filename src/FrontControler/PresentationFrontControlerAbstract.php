<?php

namespace FrontControler;


use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

use \Pes\Router\Resource\ResourceRegistryInterface;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Pes\Http\Response\RedirectResponse;
use Pes\Http\Response;
use Pes\Http\Factory\ResponseFactory;

use Pes\View\ViewInterface;

/**
 * Description of PresentationFrontControlerAbstract
 *
 * @author pes2704
 */
abstract class PresentationFrontControlerAbstract extends FrontControlerAbstract implements PresentationFrontControlerInterface {

    /**
     * @var StatusSecurityRepo
     */
    protected $statusSecurityRepo;

    /**
     *
     * @var StatusFlashRepo
     */
    protected $statusFlashRepo;

    /**
     * @var StatusPresentationRepo
     */
    protected $statusPresentationRepo;

    /**
     * @var ResourceRegistryInterface
     */
    protected $resourceRegistry;

    /**
     *
     * @param StatusSecurityRepo $statusSecurityRepo
     */
    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo
            ) {
        $this->statusSecurityRepo = $statusSecurityRepo;
        $this->statusFlashRepo = $statusFlashRepo;
        $this->statusPresentationRepo = $statusPresentationRepo;
    }

    ### headers ###

    /**
     * Přetěžuje addHeaders() z FrontControlerAbstract
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function addHeaders(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $statusPresentation = $this->statusPresentationRepo->get();
        $language = $this->statusPresentationRepo->get()->getLanguage();
        $response = $response->withHeader('Content-Language', $language->getLocale());

        $userActions = $this->statusSecurityRepo->get()->getUserActions();
        if ($userActions AND $userActions->presentEditableArticle()) {
            $response = $response->withHeader('Cache-Control', 'no-cache');
        } else {
            $response = $response->withHeader('Cache-Control', 'public, max-age=180');
        }
        $cls = (new \ReflectionClass($this))->getShortName();
        $response = $response->withHeader('X-RED-Controlled', "$cls");
        return $response;
    }

    ### response ###

    /**
     * Generuje response s přesměrováním na zadanou adresu.
     *
     * @param string $restUri Relativní adresa - resource uri
     * @return Response
     */
    protected function redirectSeeOther(ServerRequestInterface $request, $restUri) {
        $subPath = $this->getUriInfo($request)->getRootAbsolutePath();
        return RedirectResponse::withPostRedirectGet(new Response(), $subPath.ltrim($restUri, '/')); // 303 See Other
    }

    /**
     * Generuje response s přesměrování na adresu posledního GET requestu jako odpověď na POST request při použití POST-REDIRECT-GET.
     *
     * @param ServerRequestInterface $request
     * @return type
     */
    protected function redirectSeeLastGet(ServerRequestInterface $request) {
        return $this->redirectSeeOther($request, $this->statusPresentationRepo->get()->getLastGetResourcePath()); // 303 See Other
    }

    /**
     * Generuje response jako přímou odpověď na POST request.
     *
     * @param type $messageText
     * @return Response
     */
    protected function okMessageResponse($messageText) {
        // vracím 200 OK - použití 204 NoContent způsobí, že v jQuery kódu .done(function(data, textStatus, jqXHR) je proměnná data undefined a ani jqXhr objekt neobsahuje vrácený text - jQuery předpokládá, že NoContent znamená NoContent
        $response = new Response();
        $response->getBody()->write($messageText);
        return $response;
    }

    ### flash ###

    public function addFlashMessage($message) {
        $this->statusFlashRepo->get()->appendMessage($message);
    }

    ### status set get metods ###

    protected function setPresentationMenuItem($menuItem) {
        $statusPresentation = $this->statusPresentationRepo->get();
        $statusPresentation->setMenuItem($menuItem);
    }

    protected function getPresentationLangCode() {
        return $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
    }

    ### create response helpers ###

    /**
     *
     * @param ServerRequestInterface $request
     * @param ViewInterface $view
     * @return ResponseInterface
     */
    public function createResponseFromView(ServerRequestInterface $request, ViewInterface $view): ResponseInterface {

        $response = (new ResponseFactory())->createResponse();

        ####  hlavičky  ####
        $response = $this->addHeaders($request, $response);

        ####  body  ####
//        $size = $response->getBody()->write($view);
        $str = $view->getString();
        $size = $response->getBody()->write($str);
        $response->getBody()->rewind();
        return $response;
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @param ViewInterface $view
     * @return ResponseInterface
     */
    public function createResponseFromString(ServerRequestInterface $request, $stringContent): ResponseInterface {

        $response = (new ResponseFactory())->createResponse();

        ####  hlavičky  ####
        $response = $this->addHeaders($request, $response);

        ####  body  ####
        $size = $response->getBody()->write($stringContent);
        $response->getBody()->rewind();
        return $response;
    }

}
