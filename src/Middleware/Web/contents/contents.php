<?php
use Middleware\Web\AppContext;
use View\Includer;


function getNoContentMessage($lang) {
    switch ($lang) {
        case "lan1":
            $content[] = '<span class="chyba">Litujeme, ale česká verze požadované stránky není uveřejněna.</span>';
            break;
            case "lan2":
            $content[] = '<span class="chyba">Sorry, the English version of this page hasn\'t been published.</span>';
            break;
        case "lan3":
            $content[] = '<span class="chyba">Es tut uns leid, diese Seite ist in der deutschen Sprache nicht publiziert.</span>';
            break;
        default:
            break;
    }
    return implode(PHP_EOL, $content);
}

function createDatabaseContent(Psr\Container\ContainerInterface $mwContainer, $list, $lang, $edit, $klic) {
    $article = (new Model\Repository\PaperRepo($mwContainer->get(\Model\Dao\PaperDao::class)))->get($lang, $list);
    if($article) {
        if ($edit) {
            include Middleware\Web\AppContext::getScriptsDirectory().'templates/layout/body/telo/tiny_config.php';    // tinymce

            $content[] =  "<form method='POST' action='index.php?app=rs&list=stranky_publ&language=$lang&stranka='>";
            $content[] =    "<div class='articleHeadline-editable'>";
            $content[] =        "<h1 class ='tinymce-heading'>" . $article->getHeadline(). "</h1>";
            $content[] =        "<button class='ui mini basic icon button save'><i class='save green outline big icon'></i></button>";
            $content[] =    "</div>";
            $content[] =    "<div class='articleContent-editable' name='obsah'>"
                                .$article->getContent().
                            "</div>";
            $content[] = "</form>";
        } else {
            $content[] = "<div class='articleHeadline'>";
            $content[] =    "<h1>" . $article->getHeadline(). "</h1>";
            $content[] = "</div>";
            $content[] = "<div class='articleContent'>"
                            .$article->getContent().
                         "</div>";
        }
    } else {
        $content[] = getNoContentMessage($lang);
    }
    return implode(PHP_EOL, $content);

}

function createContent(Psr\Container\ContainerInterface $mwContainer, $context) {
    extract($context);
    $content = [];
    $contentSouce = [
        "mapa" => "contents/mapa.php",
        "f0" => "contents/f0.php",
        "f0_1" => "contents/f0_1.php",
        "f0_2" => "contents/f0_2.php",
//        konstStaffer_prihlasovaci_formular => Middleware\Staffer\AppContext::getScriptsDirectory()."staffer/webcontents/" .konstStaffer_prihlasovaci_formular. ".php",
//        konstStaffer_dotazovaci_formular => Middleware\Staffer\AppContext::getScriptsDirectory()."staffer/webcontents/" .konstStaffer_dotazovaci_formular. ".php",

        // a1 - a5 jsou zde reálně jen, protože se objevují ve výsledcích hledání a pak se v editačním módu objeví editovatelné v oblasti article
        "a1" =>  function(Psr\Container\ContainerInterface $mwContainer, $list, $lang, $edit, $klic){
                        /* @var $segmentAktuality \Component\Html\Segment\SegmentComponentInterface */
                        $segmentAktuality = $mwContainer->get($edit ? 'component.headlined.editable' : 'component.headlined')->setLangCodeFk($lang)->setMenuRootName('a1');
                        return $segmentAktuality->getView();
                    },
        "a2" =>   function(Psr\Container\ContainerInterface $mwContainer, $list, $lang, $edit, $klic){
                        /* @var $segmentAkce \Component\Html\Segment\SegmentComponentInterface */
                        $segmentAkce = $mwContainer->get($edit ? 'component.headlined.editable' : 'component.headlined')->setLangCodeFk($lang)->setMenuRootName('a2');
                        return $segmentAkce->getView();
                    },
        "a3" =>  function(Psr\Container\ContainerInterface $mwContainer, $list, $lang, $edit, $klic){
                        /* @var $segmentOdkazy \Component\Html\Segment\SegmentComponentInterface */
                        $segmentOdkazy = $mwContainer->get($edit ? 'component.headlined.editable' : 'component.headlined')->setLangCodeFk($lang)->setMenuRootName('a3');
                        return $segmentOdkazy->getView();
                    },
        "a4" =>   function(Psr\Container\ContainerInterface $mwContainer, $list, $lang, $edit, $klic){
                        /* @var $egmentRazitko \Component\Html\Segment\SegmentComponentInterface */
                        $egmentRazitko = $mwContainer->get($edit ? 'component.block.editable' : 'component.block')->setLangCodeFk($lang)->setMenuRootName('a4');
                        return $egmentRazitko->getView();
                    },
        "a5" =>   function(Psr\Container\ContainerInterface $mwContainer, $list, $lang, $edit, $klic){
                        /* @var $segmentSocialniSite \Component\Html\Segment\SegmentComponentInterface */
                        $segmentSocialniSite = $mwContainer->get($edit ? 'component.block.editable' : 'component.block')->setLangCodeFk($lang)->setMenuRootName('a5');
                        return $segmentSocialniSite->getView();
                    },
        "HARMONOGRAM" => "contents/v0.php",
        "VOLNE_POZICE" => "contents/v0.php",
        "v0" => "contents/v0.php",
        "hledani2"=> function(Psr\Container\ContainerInterface $mwContainer, $list, $lang, $edit, $klic){
                        /* @var $contentSearch Component\Html\Content\SearchComponent */
                        $contentSearch = $mwContainer->get('generated.search');
                        return $contentSearch->setLangCodeFk($lang)->setSearch($klic)->getView();
                    },

    ];

    if (array_key_exists($list, $contentSouce)) {
        if (is_callable($contentSouce[$list])) {
            $content[] = $contentSouce[$list]($mwContainer, $list, $lang, $edit, $klic);
        } else {
            $fileName = Middleware\Web\AppContext::getScriptsDirectory().$contentSouce[$list];
            if (file_exists ($fileName)) {
                $content[] = (new Includer())->protectedIncludeScope($fileName,
                                // všechny parametry předané při new Includer())->protectedIncludeScope(... v middleware Web/Web
                                $context );
            } else {
                $content[] = getNoContentMessage($lang);
            }
        }
    } else {                   //**** pro  zobrazeni ostatnich
        $content[] = createDatabaseContent($mwContainer, $list, $lang, $edit, $klic);
    }
    return implode(PHP_EOL, $content);
}

function replaceSlots(Psr\Container\ContainerInterface $mwContainer, $context, $content) {
    extract($context);
    //hledam v obsahu stranky vzorek -vsechny '--%sloty%--' k nahrade
    $slotPrefix = "--%";
    $slotPostfix = "%--";
    $pattern = "/($slotPrefix)(.*)($slotPostfix)/u";           //^--%.*%--$
    $matches = [];
    preg_match_all($pattern, $content, $matches);

    //pro nalezene '--%sloty%--' hledam stranku, kde list= 'slot'
    foreach ($matches[2] as $match) {
        $context['list'] = $match;   // nový list!
        $replacement = createContent($mwContainer, $context);
        if (strpos($match, "VLOZVIDEOFLV")===0) {  // priklad ocekavaneho slotu    --%VLOZVIDEOFLV_Jmenosouborubezpripony%--
            $cislo_vlozvideo=$cislo_vlozvideo+1 ;
            include_once 'flowplayer_vlozvideo.php' ;  //fce video_flv - vyrobi $script_pro_video

            $replacement = video_flv($mat,$cislo_vlozvideo);
            //vymeni slot $mat za  vyrobeny script!!!!!!
        }
        $content = str_replace($slotPrefix.$match.$slotPostfix, $replacement, $content);
    }
    return $content;
};

include Middleware\Staffer\AppContext::getScriptsDirectory().'app/app_konstanty.php';    // stafferovské konstanty
include Middleware\Rs\AppContext::getScriptsDirectory().'app/app_konstanty.php';    // sloty

echo replaceSlots($mwContainer, $context, createContent($mwContainer, $context));


