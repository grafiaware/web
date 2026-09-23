<?php

namespace FrontControler\Response;

use FrontControler\StatusEnum;

use Status\Model\Repository\StatusPresentationRepo;

use Pes\Http\Request;
use Pes\Http\Helper\UriInfoInterface;
use Pes\Http\Factory\ResponseFactory;
use Pes\Http\Response\RedirectResponse;
use Pes\Http\Response;
use Pes\View\ViewInterface;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use LogicException;
use UnexpectedValueException;

/**
 * 1:1 extrakce vytváření response z FrontControlerAbstract.
 *
 * Hlavičky Content-Language / Cache-Control aplikuje přes callbacky z kontroleru
 * (PresentationFrontControlerAbstract přetěžuje cache headers).
 */
class HttpResponseFactory implements ResponseFactoryInterface {

    /**
     * @param callable(ResponseInterface): ResponseInterface $applyCacheHeaders
     * @param callable(ResponseInterface): ResponseInterface $applyContentHeaders
     */
    public function __construct(
        private StatusPresentationRepo $statusPresentationRepo,
        private $applyCacheHeaders,
        private $applyContentHeaders,
    ) {
    }

    public function createStringOKResponseFromView(ViewInterface $view, $status = StatusEnum::_200_OK): ResponseInterface {
        $statusEnumValue = $this->statusCode($status);
        $stringContent = $view->getString();
        if (is_null($stringContent)) {
            $cls = get_class($view);
            $stringContent = "No string content returned by $cls method getString().";
        }
        return $this->createStringOKResponse($stringContent, $statusEnumValue);
    }

    public function createStringOKResponse($stringContent, $status = StatusEnum::_200_OK): ResponseInterface {
        $statusEnumValue = $this->statusCode($status);
        $response = (new ResponseFactory())->createResponse($statusEnumValue);

        $response = ($this->applyContentHeaders)($response);
        $response = ($this->applyCacheHeaders)($response);

        $body = $response->getBody();
        $body->write($stringContent);
        $body->rewind();
        return $response;
    }

    public function createJsonOKResponse($array, $status = StatusEnum::_200_OK): ResponseInterface {
        $statusEnumValue = $this->statusCode($status);
        $json = $this->jsonEncode($array);
        $response = $this->createStringOKResponse($json)->withStatus($statusEnumValue);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function createPutNoContentResponse($status = StatusEnum::_204_NoContent): ResponseInterface {
        $statusEnumValue = $this->statusCode($status);
        $response = (new ResponseFactory())->createResponse($statusEnumValue);
        return ($this->applyCacheHeaders)($response);
    }

    public function createJsonPostCreatedResponse($array, $status = StatusEnum::_201_Created): ResponseInterface {
        $statusEnumValue = $this->statusCode($status);
        $json = $this->jsonEncode($array);
        $response = $this->createStringOKResponse($json)->withStatus($statusEnumValue);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function createUnauthorizedResponse(): ResponseInterface {
        return (new ResponseFactory())->createResponse()->withStatus(401);
    }

    public function createResponseRedirectSeeOther(ServerRequestInterface $request, $restUri): ResponseInterface {
        $newPath = $this->getUriInfo($request)->getRootAbsolutePath() . ltrim($restUri, '/');
        return RedirectResponse::withPostRedirectGet(new Response(), $newPath);
    }

    public function redirectSeeLastGet(ServerRequestInterface $request): ResponseInterface {
        $lastGet = $this->statusPresentationRepo->getClone();
        return $this->createResponseRedirectSeeOther($request, isset($lastGet) ? $lastGet->getLastGetResourcePath() : '/');
    }

    private function statusCode($statusEnumValue) {
        return (new StatusEnum())($statusEnumValue);
    }

    private function jsonEncode($array) {
        $json = json_encode($array);
        if ($json === false) {
            throw new UnexpectedValueException("invalid value foe creating json.");
        }
        return $json;
    }

    private function getUriInfo(ServerRequestInterface $request): UriInfoInterface {
        $uriInfo = $request->getAttribute(Request::URI_INFO_ATTRIBUTE_NAME);
        if (!$uriInfo instanceof UriInfoInterface) {
            throw new LogicException(
                "Atribut requestu " . Request::URI_INFO_ATTRIBUTE_NAME
                . " neobsahuje objekt typu " . UriInfoInterface::class . "."
            );
        }
        return $uriInfo;
    }
}
