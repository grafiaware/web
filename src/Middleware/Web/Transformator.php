<?php

namespace Middleware\Web;


use Site\Configuration;

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

use Model\Dao\MenuItemDao;
use Model\Repository\StatusPresentationRepo;
use Model\Repository\StatusFlashRepo;

use Pes\Http\Body;

/**
 * Description of Transformator
 * Transformuje obsahy stránek uložených v původní verzi rs.
 *
 * @author pes2704
 */
class Transformator extends AppMiddlewareAbstract implements MiddlewareInterface {

    /**
     * @var ContainerInterface
     */
    private $container;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

        $this->container =
                (new HierarchyContainerConfigurator())->configure(
                    (new WebContainerConfigurator())->configure(
                        (new DbUpgradeContainerConfigurator())->configure(
                                new Container($this->getApp()->getAppContainer())
                        )
                    )
                );

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
// <a target="_blank" href="../index.php?list=s01_08_03&language=lan1&lay=">
// <img width="234" height="167" alt="soubor plat.gif" src="files/1096212.gif" style="color: #ffffff; float: right;">
        $filesDirectory = Configuration::transformator()['filesDirectory'];
        $publicDirectory = Configuration::transformator()['publicDirectory'];
        $siteDirectory = Configuration::transformator()['siteDirectory'];
//        $transform = array(
//            'src="images/'               => 'src="'.$siteDirectory.'images/',   // <img>
//            'srcset="images/'               => 'srcset="'.$siteDirectory.'images/',   // <img>
//
//            'src="video/'               => 'src="'.$filesDirectory.'video/',   // <video>
//            'href="video/'               => 'href="'.$filesDirectory.'video/',   // video fallback pro stžení <a href=... >
//            'src="files/'            => 'src="'.$filesDirectory.'files/',   //
//            'src="../files/'            => 'src="'.$filesDirectory.'files/',   // pro chybně zadané obrázky (s tečkami)
//            'href="files/'              => 'href="'.$filesDirectory.'files/',  // pro download
//
//            'src="public/web/'=>'src="'.$publicDirectory,
//        );
        $transform = array(
            '"layout-images/'               => '"'.$siteDirectory.'layout-images/',   // <img>
            '"images/'               => '"'.$filesDirectory.'images/',   // <video>

            '"movies/'               => '"'.$filesDirectory.'movies/',   // <video>
            '"files/'            => '"'.$filesDirectory.'files/',   //
            '"../files/'            => '"'.$filesDirectory.'files/',   // pro chybně zadané obrázky (s tečkami)

            '"public/web/'=>'"'.$publicDirectory,
        );
        $first = str_replace(array_keys($transform), array_values($transform), $text);
        $transformUrls = $this->transformUrls($first);
        // str_replace při použití polí pro záměnu provádí náhrady posrupně znovu v celém řetězci pro každý prvek pole záměn
        // pokud je v poli záměn řetězec, který je podřetězcem řetězce, který je v poli záměn později dojde k záměně podřetězce - např. chci nahradit
        // ada za mařka a adam2 za božena3 -> v řetězci adam2 je nahrazen podřetězec ada a vznikne -> mařkam2 - a ta už tam zůstane
        // nutné provést setřídění podle klíčů v reverzním pořadí
        krsort($transformUrls);
        $second = str_replace(array_keys($transformUrls), array_values($transformUrls), $first);
        return $second;
    }

    /**
     * url odkazy na vlastní stránky (do menu) ve starých obsazích -transformace na rest url
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
                        $row = $dao->getByList($langCode, $pairs[$key]);
                        if ($row) {
                            $transform[$url] = "www/item/$langCode/{$row['uid_fk']}";
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
                $statusFlashRepo->get()->appendMessage("Nenalezen odkaz $url v databázi.");
//                user_error("Nenalezen odkaz $url v databázi.", E_USER_WARNING);
                if ($this->hasLogger()) {
                    $this->getLogger()->notice("Pro uri $requestUri nenalezen v obsahu stránky v databázi odkaz $url.");
                }
            }

        }
        return $transform;
    }
}