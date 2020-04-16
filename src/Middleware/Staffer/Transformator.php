<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Middleware\Staffer;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Pes\Http\Body;
/**
 * Description of Transformator
 * Transformuje obsah generovaný původní verzí edun.
 *
 * @author pes2704
 */
class Transformator implements MiddlewareInterface {

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $response = $handler->handle($request);
        $newBody = new Body(fopen('php://temp', 'r+'));
        $newBody->write($this->transform($response->getBody()->getContents()));
        return $response->withBody($newBody);
    }

    /**
     * Transformuje text záměnou všech řetězců definovaných v poli $search za řetězce z pole $replace.
     * @param type $text
     * @return type
     */
    private function transform($text) {
        $appPublicDirectory = \Middleware\Edun\AppContext::getAppPublicDirectory();
        $transform = array(
            'src="img/' => 'src="'.$appPublicDirectory.'img/',
            'src="files/' => 'src="'.$appPublicDirectory.'files/',
        );
//        echo "<pre>".htmlentities($text)."</pre>";
//        $test = str_replace(array_keys($transform), array_values($transform), $text, $count);
//        echo "<p>Provedeno $count záměn.</p>";
//        echo "<pre>".htmlentities($test)."</pre>";
        return str_replace(array_keys($transform), array_values($transform), $text);
    }
}
