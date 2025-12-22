<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPInterface.php to edit this template
 */

namespace Web\Middleware\Page\Controler;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 *
 * @author pes2704
 */
interface PageControlerInterface {
    public function home(ServerRequestInterface $request): ResponseInterface ;
    public function item(ServerRequestInterface $request, $uid): ResponseInterface;
    //TODO: SV search result -> komponent + zrevidovat web middleware api
    public function searchResult(ServerRequestInterface $request): ResponseInterface;
}
