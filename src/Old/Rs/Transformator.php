<?php

namespace Middleware\Rs;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Pes\Http\Body;

use Middleware\Rs\AppContext;

/**
 * Description of Transformator
 * Transformuje obsahy stránek uložených v původní verzi rs.
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
        $publicDirectory = AppContext::getAppPublicDirectory();
        $transform = array(
            " href='index.php'"   => " href='index.php?app=rs'",  // přidán get parametr app=rs - počátční mezera brání záměne onClick="location.href=...
            ' href="index.php"'   => ' href="index.php?app=rs"',  // přidán get parametr app=rs
            " href='index.php?"   => " href='index.php?app=rs&",  // přidán get parametr app=rs
            ' href="index.php?'   => ' href="index.php?app=rs&',  // přidán get parametr app=rs
            "action='index.php'"   => "action='index.php?app=rs'",  // přidán get parametr app=rs pro formuláře
            'action="index.php"'   => 'action="index.php?app=rs"',  // přidán get parametr app=rs pro formuláře
            "action='index.php?'"  => "action='index.php?app=rs&'",  // přidán get parametr app=rs pro formuláře
            'action="index.php?"'   => 'action="index.php?app=rs&"',  // přidán get parametr app=rs pro formuláře
            //
            // transformace z web (pro obsahy z db) - stejná jako v Web\Middleware\Page\Transformator
            'href="../index.php?list'   => 'href="?list',  // změněna cesta k souboru index.php - odkazy na vlastní stránky (do menu) ve starých obsazích
            'src="files/'               => 'src="'.$publicDirectory.'files/',   // změněna cesta ke složce files
            'src="../files/'            => 'src="'.$publicDirectory.'files/',   // změněna cesta ke složce files - pro chybně zadané obrázky (s tečkou)
            'href="files/'              => 'src="'.$publicDirectory.'files/',
        );
//        echo "<pre>".htmlentities($text)."</pre>";
//        $test = str_replace(array_keys($transform), array_values($transform), $text, $count);
//        echo "<p>Provedeno $count záměn.</p>";
//        echo "<pre>".htmlentities($test)."</pre>";
        return str_replace(array_keys($transform), array_values($transform), $text);
    }
}