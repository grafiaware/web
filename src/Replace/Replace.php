<?php
namespace Replace;

use Site\ConfigurationCache;
use Psr\Http\Message\ServerRequestInterface;

use Pes\Application\AppFactory;
use Pes\Application\UriInfoInterface;

use Red\Model\Dao\MenuItemDaoInterface;
use Status\Model\Entity\StatusPresentationInterface;
use Replace\Exception\InvalidHtmlSourceException;
use Replace\Exception\ListValueNotFoundInDatabaseException;
use LogicException;

/**
 * Description of Replace
 *
 * @author pes2704
 */
class Replace implements ReplaceInterface {
    
    /**
     * Transformuje text záměnou všech řetězců definovaných v poli náhrad.
     * 
     * @param ServerRequestInterface $request
     * @param type $text Předáno referencí
     * @return void
     */
    public function replaceTemplateStrings(ServerRequestInterface $request, &$text): void {

        $transform = array(
            // templates, static
            '@download/'               => ConfigurationCache::files()['@download'],
            '@commonimages/'           => ConfigurationCache::files()['@commonimages'],
            '@commonmovies/'           => ConfigurationCache::files()['@commonmovies'],
            '@siteimages/'             => ConfigurationCache::files()['@siteimages'],
            '@sitemovies/'             => ConfigurationCache::files()['@sitemovies'],
            '@presenter'               => ConfigurationCache::files()['@presenter'],
        );

        // <a href="index.php?list=download&amp;file=1197476.txt" target="_blank">Obchodní podmínky e-shop Grafia ke stažení</a>
        $text = str_replace(array_keys($transform), array_values($transform), $text);
    }
    
    /**
     * Transformuje text záměnou všech řetězců definovaných v poli náhrad.
     * 
     * @param ServerRequestInterface $request
     * @param type $text Předáno referencí
     * @return void
     */
    public function replaceRSStrings(ServerRequestInterface $request, &$text): void {

        $publicDirectory = ConfigurationCache::transformator()['publicDirectory'];
        $siteDirectory = ConfigurationCache::transformator()['siteDirectory'];
        $filesDirectory = ConfigurationCache::transformator()['filesDirectory'];

        $transform = array(         
            // staré stránky rs
            '"../files/'            => '"'.$filesDirectory,   // pro chybně zadané obrázky (s dvěma tečkami)
            '"files/'            => '"'.$filesDirectory,
            '"../index.php'            => '"index.php',  // pro odkazy do rs - vznikly asi zkopírováním kódu z etevřené editované stránky

            '"layout-images/' => '"'.$siteDirectory.'layout-images/',
            '"public/web/'=>'"'.$publicDirectory,
            'font-size: x-small;' => '',
            'font-size: small;' => '',
            'font-size: medium;' => '',
//            '<span style="font-size: 10pt;' => '<span style="font-size: 12pt;',
//            '<span style="font-size: 12pt;' => '<span style="font-size: 14pt;',
            'font-size: 8pt;' => '',
            'font-size: 9pt;' => '',
            'font-size: 10pt;' => '',
            'font-size: 11pt;' => '',
            'font-size: 12pt;' => '',
            'font-size: 14pt;' => '',
            'font-size: 17pt;' => '',
            'font-size: 18pt;' => '',
);

        // <a href="index.php?list=download&amp;file=1197476.txt" target="_blank">Obchodní podmínky e-shop Grafia ke stažení</a>
        $text = str_replace(array_keys($transform), array_values($transform), $text);
    }

    
    public function replaceSlots(&$text): void {
        $repl = $text;
        $slots = [
          '/(--%VLOZVIDEOFLV_)(.*)(%--)/'=>  '<video width="600px" controls  poster="'.ConfigurationCache::files()['@sitemovies'].'$2.jpg"'
            . '<source src="'.ConfigurationCache::files()['@sitemovies'].'$2.mp4" type="video/mp4">'
                . 'Váš prohlížeč nepodporuje element video.</video>' 
        ];
        foreach ($slots as $slot => $value) {
            $repl = preg_replace($slot, $value, $repl);
        }

//        --%VLOZVIDEOFLV_200127 Studio Z_Grafia_Veletrh_prace_2020%--
//        
//        <video width="600px" controls  poster="./video/200127 Studio Z_Grafia_Veletrh_prace_2020.jpg">
//        <source src="./video/200127 Studio Z_Grafia_Veletrh_prace_2020.mp4" type="video/mp4">
//         Váš prohlížeč nepodporuje element video.
//        </video>
        $text = $repl;
    }
    
    public function replaceRsUrlsInHref(ServerRequestInterface $request, &$text, $key, MenuItemDaoInterface $dao, StatusPresentationInterface $statusPresentation): void {
        $prefix = 'href="';
        $langCode = $statusPresentation->getLanguage()->getLangCode();
        $lastGetResourcePath = $statusPresentation->getLastGetResourcePath();
        $prefixLength = strlen($prefix);
        $transform = '';
        $hrefLastChar = 0;
        $notFound = [];
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
                                $notFound[] = $url;
                                $transform .= $url;
                            }
                        }
                        // tato část ničila absolutní odkazy s anchorem, myslím, že není třeba, není třeba transformovat href="#A"
                    } elseif(isset ($anchor)) {  // odkaz na kotvu na téže stránce - např. href="#A"                        
                        $path = parse_url($url, PHP_URL_PATH);
                        if ($path) {  // relativní neboi absolutní odkaz s path
                            $newUrl = $sub.trim("$path#$anchor", "/");                            
                        } else {      // relativní adkaz bez path -> má jen anchor (href="#Adam") nebo nic  
                            $newUrl = $sub.trim("$lastGetResourcePath#$anchor", "/");   
                        }
                        $transform .= $newUrl;
                    } else {
                        $transform .= $url;
                    }
                } else {
                    $text = $transform;
                    throw new InvalidHtmlSourceException("Chyba při transformaci url v obsahu. Url: '$requestUri'.");
                }
            } else {
                $transform .= substr($text, $hrefLastChar);                        
            }
        } while ($prefixFound!==false);
        if ($notFound) {
            $text = $transform;
            $strNotFound = implode(", ", $notFound);
            throw new ListValueNotFoundInDatabaseException("Nenalezen odkaz v databázi. Seznam: $strNotFound");
        }
        $text = $transform;        
    }
    
    /**
     * Pomocná metoda - získá hodnotu uriInfo z objektu UriInfo, který byl vložen do requestu jako atribut s jménem AppFactory::URI_INFO_ATTRIBUTE_NAME v AppFactory.
     *
     * @return UriInfoInterface
     */
    private function getUriInfo(ServerRequestInterface $request): UriInfoInterface {
        $uriInfo = $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME);
        if (! $uriInfo instanceof UriInfoInterface) {
            throw new LogicException("Atribut requestu ".AppFactory::URI_INFO_ATTRIBUTE_NAME." neobsahuje objekt typu ".UriInfoInterface::class.".");
        }
        return $uriInfo;
    }
    
}
