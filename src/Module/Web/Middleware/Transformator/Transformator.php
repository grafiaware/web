<?php

namespace Web\Middleware\Transformator;


use Site\ConfigurationCache;

use Pes\Middleware\AppMiddlewareAbstract;
use Pes\Container\Container;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Container\WebContainerConfigurator;
use Container\DbUpgradeContainerConfigurator;
use Container\HierarchyContainerConfigurator;

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

        $startTime = microtime(true);
        $this->container = $this->getApp()->getAppContainer();
        $newBody = new Body(fopen('php://temp', 'r+'));
        $newBody->write($this->transform($response->getBody()->getContents()));
//        $newBody->write($response->getBody()->getContents());
        $response = $response->withHeader(self::HEADER, sprintf('%2.3fms', (microtime(true) - $startTime) * 1000));
        return $response->withBody($newBody);
    }

    /**
     * Transformuje text záměnou všech řetězců definovaných v poli $search za řetězce z pole $replace.
     * @param type $text
     * @return type
     */
    private function transform($text) {

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
            '"files/'            => '"'.$filesDirectory,
            '"../files/'            => '"'.$filesDirectory,   // pro chybně zadané obrázky (s dvěma tečkami)

            '"layout-images/' => '"'.$siteDirectory.'layout-images/',
            '"public/web/'=>'"'.$publicDirectory,
        );

        $first = str_replace(array_keys($transform), array_values($transform), $text);
        $transformUrls = $this->transformUrls($first);
        $second = str_replace(array_keys($transformUrls), array_values($transformUrls), $first);
        return $second;
    }

    /**
     * url odkazy na vlastní stránky (do menu) ve starých obsazích (rs) -transformace na rest url (RED)
     * Generuje pole, kde staré url jsou klíče a nové url je hodnota.
     * Pole setřídí podle klíčů v reverzním pořadí tak, aby při nahrazování byl nahrazen nejprve delší klíč (staré url) a teprve pak kratší klíč.
     *
     * @param type $text
     * @return type
     */
    private function transformUrls($text) {
        // <a href="index.php?list=download&amp;file=1197476.txt" target="_blank">Obchodní podmínky e-shop Grafia ke stažení</a>
        $prefix = 'href="';
        $key = 'list';
        $length = strlen($prefix);
        $dao = $this->container->get(MenuItemDao::class);
        /** @var StatusPresentationRepo $statusPresentationRepo */
        $statusPresentationRepo = $this->container->get(StatusPresentationRepo::class);
        $langCode = $statusPresentationRepo->get()->getLanguage()->getLangCode();
        $transform = [];
        $end = 0;
        $notFound = [];
        do {
            $begin = strpos($text, $prefix, $end);
            if ($begin !== false) {
                $begin = $begin+$length;
                $end = strpos($text, '"', $begin);
                if ($end !== false) {
                    $url = substr($text, $begin, $end-$begin);
                    $query = parse_url($url, PHP_URL_QUERY);
                    parse_str($query, $pairs);
                    if (array_key_exists($key, $pairs)) {
                        $row = $dao->getByList(['lang_code_fk'=>$langCode, 'list'=>$pairs[$key]]);
                        if ($row) {
                            $transform[$url] = "web/v1/page/item/{$row['uid_fk']}";
                        } else {
                            $notFound[] = $url;

                        }
                    }
                }
            }

        } while ($begin!==false);
        if ($notFound) {
            $requestUri = $this->getApp()->getServerRequest()->getUri()->getPath();
            /** @var StatusFlashRepo $statusFlashRepo */
            $statusFlashRepo = $this->container->get(StatusFlashRepo::class);
            foreach ($notFound as $url) {
                if (PES_DEVELOPMENT) {
                    $statusFlashRepo->get()->setMessage("Nenalezen odkaz $url v databázi.");
                }
                if ($this->hasLogger()) {
                    $this->getLogger()->notice("Pro uri $requestUri nenalezen v obsahu stránky v databázi odkaz $url.");
                }
            }

        }
        // str_replace při použití polí pro záměnu provádí náhrady postupně znovu v celém řetězci pro každý prvek pole záměn
        // pokud je v poli záměn řetězec, který je podřetězcem řetězce, který je v poli záměn později dojde k záměně podřetězce - např. chci nahradit
        // ada za mařka a adam2 za božena3 -> v řetězci adam2 je nahrazen podřetězec ada a vznikne -> mařkam2 - a ta už tam zůstane
        // nutné provést setřídění podle klíčů v reverzním pořadí
        krsort($transform);
        return $transform;
    }
}