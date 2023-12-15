<?php

namespace Web\Middleware\Transformator;


use Site\ConfigurationCache;

use Pes\Middleware\AppMiddlewareAbstract;
use Pes\Container\Container;
use Pes\Application\AppFactory;
use Pes\Application\UriInfoInterface;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Red\Model\Dao\MenuItemDao;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;

use Pes\Http\Body;

/**
 * Description of Transformator
 * Transformuje obsahy stránek uložených v původní verzi rs.
 *
 * @author pes2704
 */
class Transformator extends AppMiddlewareAbstract implements MiddlewareInterface {

    const HEADER = 'X-RED-Transformation-Time';

    /**
     * @var ContainerInterface
     */
    private $container;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

        $response = $handler->handle($request);
        if ($request->getMethod()=="GET") {
            $startTime = microtime(true);
            $this->container = $this->getApp()->getAppContainer();  // měl by mít nastaven kontejner z middleware web
            $newBody = new Body(fopen('php://temp', 'r+'));
            $uri = $request->getUri();
            $newBody->write($this->transform($request, $response->getBody()->getContents()));
            $response = $response->withHeader(self::HEADER, sprintf('%2.3fms', (microtime(true) - $startTime) * 1000));
            $response = $response->withBody($newBody);
        }
        return $response;
    }

    /**
     * Transformuje text záměnou všech řetězců definovaných v poli $search za řetězce z pole $replace.
     * @param type $text
     * @return type
     */
    private function transform(ServerRequestInterface $request, $text) {

        $downloadDirectory = ConfigurationCache::files()['@download'];
        $siteImagesDirectory = ConfigurationCache::files()['@siteimages'];
        $siteMoviesDirectory = ConfigurationCache::files()['@sitemovies'];
        $commonImagesDirectory = ConfigurationCache::files()['@commonimages'];
        $commonMoviesDirectory = ConfigurationCache::files()['@commonmovies'];
        $filesDirectory = ConfigurationCache::files()['files'];

        $publicDirectory = ConfigurationCache::transformator()['publicDirectory'];
        $siteDirectory = ConfigurationCache::transformator()['siteDirectory'];

        $transform = array(
            // RED
            '@download/'               => $downloadDirectory,
            '@commonimages/'           => $commonImagesDirectory,
            '@commonmovies/'           => $commonMoviesDirectory,
            '@siteimages/'             => $siteImagesDirectory,
            '@sitemovies/'             => $siteMoviesDirectory,

            // staré stránky rs
            '"../files/'            => '"'.$filesDirectory,   // pro chybně zadané obrázky (s dvěma tečkami)
            '"files/'            => '"'.$filesDirectory,
            '"../index.php'            => '"index.php',  // pro odkazy do rs - vznikly asi zkopírováním kódu z etevřené editované stránky

            '"layout-images/' => '"'.$siteDirectory.'layout-images/',
            '"public/web/'=>'"'.$publicDirectory,
            'font-size: x-small;' => '',
            'font-size: small;' => '',
            'font-size: medium;' => '',
        );

        $first = str_replace(array_keys($transform), array_values($transform), $text);
        $second = $this->transformUrls($request, $first);
        return $second;
    }

    /**
     * url odkazy na vlastní stránky (do menu) ve starých obsazích (rs) -transformace na rest url (RED)
     * Generuje pole, kde staré url jsou klíče a nové url je hodnota.
     * Pole setřídí podle klíčů v reverzním pořadí tak, aby při nahrazování byl nahrazen nejprve delší klíč (staré url) a teprve pak kratší klíč.
     *
     * @param type $text
     * @return string
     */
    private function transformUrls(ServerRequestInterface $request, $text): string {
        // <a href="index.php?list=download&amp;file=1197476.txt" target="_blank">Obchodní podmínky e-shop Grafia ke stažení</a>
        $prefix = 'href="';
        $key = 'list';
        $prefixLength = strlen($prefix);
        $dao = $this->container->get(MenuItemDao::class);
        /** @var StatusPresentationRepo $statusPresentationRepo */
        $statusPresentationRepo = $this->container->get(StatusPresentationRepo::class);
        $langCode = $statusPresentationRepo->get()->getLanguage()->getLangCode();
        $lastGetResourcePath = $statusPresentationRepo->get()->getLastGetResourcePath();
        $transform = '';
        $hrefLastChar = 0;
        
        do {
            $prefixFound = strpos($text, $prefix, $hrefLastChar);
            if ($prefixFound !== false) {
                $hrefFirstChar = $prefixFound+$prefixLength;
                $transform .= substr($text, $hrefLastChar, $hrefFirstChar-$hrefLastChar);  // tady je $hrefLastChar pozice posledního znaku z minulého cyklu
                $hrefLastChar = strpos($text, '"', $hrefFirstChar);
                if ($hrefLastChar !== false) {                    
                    $url = substr($text, $hrefFirstChar, $hrefLastChar-$hrefFirstChar);
                    $query = parse_url($url, PHP_URL_QUERY);
                    $anchor = parse_url($url, PHP_URL_FRAGMENT);
                    $sub = $this->getUriInfo($request)->getSubdomainPath();
                    if (isset($query)) {
                        $pairs = [];
                        parse_str($query, $pairs);
                        if (array_key_exists($key, $pairs)) {
                            $row = $dao->getByList(['lang_code_fk'=>$langCode, 'list'=>$pairs[$key]]);
                            if ($row) {
                                $newUrl = $sub."web/v1/page/item/{$row['uid_fk']}".($anchor ? "#$anchor" : "");
                                $transform .= $newUrl;
                            } else {
                                $this->flashAndLogNotFound($url);
                                $transform .= $url;
                            }
                        }
                    } elseif(isset ($anchor)) {  // odkaz na kotvu na téže stránce - např. href="#A"                        
                        $newUrl = $sub.trim("$lastGetResourcePath#$anchor", "/");
                        $transform .= $newUrl;
                    } else {
                        $transform .= $url;                        
                    }
                } else {
                    $this->flashAndLogIncorrectHtmlSyntax($request);
                }
            } else {
                $transform .= substr($text, $hrefLastChar);                        
            }
        } while ($prefixFound!==false);
        return $transform;
    }
    
    private function flashAndLogNotFound($url) {
        $requestUri = $this->getApp()->getServerRequest()->getUri()->getPath();
        /** @var StatusFlashRepo $statusFlashRepo */
        $statusFlashRepo = $this->container->get(StatusFlashRepo::class);
        if (PES_DEVELOPMENT) {
            $statusFlashRepo->get()->setMessage("Nenalezen odkaz $url v databázi.");
        }
        if ($this->hasLogger()) {
            $this->getLogger()->notice("Pro uri $requestUri nenalezen v obsahu stránky v databázi odkaz $url.");
        }
    }
    private function flashAndLogIncorrectHtmlSyntax(ServerRequestInterface $request) {
        $requestUri = $request->getUri()->getPath();
        /** @var StatusFlashRepo $statusFlashRepo */
        $statusFlashRepo = $this->container->get(StatusFlashRepo::class);        
        if (PES_DEVELOPMENT) {
            $statusFlashRepo->get()->setMessage("Chyba při transformaci obsahu. Url: '$requestUri'.");
        }
        if ($this->hasLogger()) {
            $this->getLogger()->notice("Chyba při transformaci obsahu. Nalezen počáteční řetězec a nenalezen konec. Url: '$requestUri'.");
        }
    }
    /**
     * Pomocná metoda - získá base path z objektu UriInfo, který byl vložen do requestu jako atribut s jménem AppFactory::URI_INFO_ATTRIBUTE_NAME v AppFactory.
     *
     * @return UriInfoInterface
     */
    protected function getUriInfo(ServerRequestInterface $request): UriInfoInterface {
        $uriInfo = $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME);
        if (! $uriInfo instanceof UriInfoInterface) {
            throw new LogicException("Atribut requestu ".AppFactory::URI_INFO_ATTRIBUTE_NAME." neobsahuje objekt typu ".UriInfoInterface::class.".");
        }
        return $uriInfo;
    }
    
    /**
     * Vrací base path pro nastavení html base path
     * @param ServerRequestInterface $request
     * @return string
     */
    private function getBasePath(ServerRequestInterface $request) {
        $basePath = $this->getUriInfo($request)->getSubdomainPath();
        return $basePath;
    }
}