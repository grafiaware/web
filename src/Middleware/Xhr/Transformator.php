<?php

namespace Middleware\Xhr;

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
                    (new DbUpgradeContainerConfigurator())->configure(
                        (new WebContainerConfigurator())->configure(
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
        $filesDirectory = AppContext::getFilesDirectory();
        $transform = array(
            'src="files/'               => 'src="'.$filesDirectory.'files/',   // změněna cesta ke složce files
            'src="../files/'            => 'src="'.$filesDirectory.'files/',   // změněna cesta ke složce files - pro chybně zadané obrázky (s tečkami)
            'href="files/'              => 'href="'.$filesDirectory.'files/',
            'src="public/web/'=>'src="/web/'.AppContext::getAppPublicDirectory(),  // relativně k documentroot (localhost)
        );
        $first = str_replace(array_keys($transform), array_values($transform), $text);
        $transformUrls = $this->transformUrls($first);
        $second = str_replace(array_keys($transformUrls), array_values($transformUrls), $first);
        return $second;
    }

    /**
     * url odkazy na vlastní stránky (do menu) ve starých obsazích -transformace na rest url
     * @param type $text
     * @return type
     */
    private function transformUrls($text) {
        $prefix = 'href="';
        $key = 'list';
        $length = strlen($prefix);
        $dao = $this->container->get(MenuItemDao::class);
        /** @var StatusPresentationRepo $statusPresentationRepo */
        $statusPresentationRepo = $this->container->get(StatusPresentationRepo::class);
        $langCode = $statusPresentationRepo->get()->getLanguage()->getLangCode();
        $transform = [];
        $end = 0;

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
                        }
                    }
                }
            }

        } while ($begin!==false);
        return $transform;
    }
}