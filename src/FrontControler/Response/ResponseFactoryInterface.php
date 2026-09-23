<?php

namespace FrontControler\Response;

use FrontControler\StatusEnum;
use Pes\View\ViewInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Skládá PSR-7 response (status, hlavičky, body).
 * Odděleno z FrontControlerAbstract — kontrolery jen delegují.
 */
interface ResponseFactoryInterface {

    public function createStringOKResponseFromView(ViewInterface $view, $status = StatusEnum::_200_OK): ResponseInterface;

    public function createStringOKResponse($stringContent, $status = StatusEnum::_200_OK): ResponseInterface;

    public function createJsonOKResponse($array, $status = StatusEnum::_200_OK): ResponseInterface;

    public function createJsonPostCreatedResponse($array, $status = StatusEnum::_201_Created): ResponseInterface;

    public function createPutNoContentResponse($status = StatusEnum::_204_NoContent): ResponseInterface;

    public function createUnauthorizedResponse(): ResponseInterface;

    /**
     * 303 See Other na zadanou rest URI (relativní resource path).
     */
    public function createResponseRedirectSeeOther(ServerRequestInterface $request, $restUri): ResponseInterface;

    /**
     * PRG: 303 na last GET resource path z Presentation statusu.
     */
    public function redirectSeeLastGet(ServerRequestInterface $request): ResponseInterface;
}
