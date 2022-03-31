<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace View;

/**
 * Description of Thief
 *
 * @author pes2704
 */
class Thief {

    public function steal($urlPrefix, $src) {
//                $menuItemId = (new RequestParams())->getParam($request, 'menu_item_id');  // jméno POST proměnné je vytvořeno v paper rendereru
//        $html = (new RequestParams())->getParam($request, 'paper_template_html');  // jméno POST proměnné je vytvořeno v paper rendereru

//        $urlPrefix = "http://praci.najdisi.cz/";
//        $src = "index.php?list=l02&language=lan1&lay=";
//        $html = file_get_contents($urlPrefix."index.php?list=l01&language=lan1&lay=");
        $html = file_get_contents($urlPrefix.$src);
        $htmlDecoded = html_entity_decode($html, ENT_HTML5);
        $htmlDecoded = str_replace(PHP_EOL, '', $htmlDecoded);
        if ($htmlDecoded) {
            $dom = new \DOMDocument('1.0', 'utf-8');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput       = true;
//            $dom->loadHTML($htmlDecoded);
            $this->loadHtml($dom, $htmlDecoded);

            $articleElement = $dom->getElementById('contents');
//            $value = $this->get_inner_html($articleElement);
            $value = $articleElement->ownerDocument->saveHTML($articleElement); //$this->get_inner_html($articleElement);
            $value = str_replace('src="files/', 'src="'.$urlPrefix.'files/', $value);
            $value = str_replace('href="../', 'href="'.$urlPrefix, $value);
            $value = str_replace('poster="./', 'poster="'.$urlPrefix, $value);
            $value = str_replace('poster="./video/', 'poster="'.$urlPrefix.'video/', $value);
            $value = str_replace('src="./video/', 'src="'.$urlPrefix.'video/', $value);
        } else {
            $value = '<p></p>';
        }

        return $value;
    }

    ######################

    private function loadHtml($layoutDocument, $text) {
//libxml_use_internal_errors(TRUE);
//// Do your load here
//$errors = libxml_get_errors();
//
//foreach ($errors as $error)
//{
//    /* @var $error LibXMLError */
//}
//
//Here is a print_r() of a single error:
//
//LibXMLError Object
//(
//    [level] => 2
//    [code] => 801
//    [column] => 17
//    [message] => Tag section invalid
//
//    [file] =>
//    [line] => 39
//)

        @$layoutDocument->loadHTML(
"<!DOCTYPE html>
<!--

-->
<html>
    <head>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/>
    </head>
    <body>"
                .
                $text
                .
    "</body>
</html>"
           );
    }

    private function get_inner_html( $node ) {
        $innerHTML= '';
        $children = $node->childNodes;
        /** @var \DOMNode $child */
        foreach ($children as $child) {
            $nn = $child->nodeName;
            if ($child->nodeName != "script") {
                $innerHTML .= $child->ownerDocument->saveHTML( $child );
            }
        }

        return $innerHTML;
    }

}
