<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Middleware\Redactor\Controller;

use FrontController\PresentationFrontControllerAbstract;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Application\AppFactory;
use Pes\Http\Request\RequestParams;
use Pes\Http\Response;
use Pes\Http\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;


use Model\Entity\Paper;

use Status\Model\Repository\{
    StatusSecurityRepo, StatusFlashRepo, StatusPresentationRepo
};
use Red\Model\Repository\{
    PaperRepo
};

/**
 * Description of PostController
 *
 * @author pes2704
 */
class PaperController extends PresentationFrontControllerAbstract {

    private $paperRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            PaperRepo $paperRepo) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->paperRepo = $paperRepo;
    }

    public function create(ServerRequestInterface $request): ResponseInterface {
        $menuItemId = (new RequestParams())->getParam($request, 'menu_item_id');  // jméno POST proměnné je vytvořeno v paper rendereru
        $html = (new RequestParams())->getParam($request, 'paper_template_html');  // jméno POST proměnné je vytvořeno v paper rendereru
        $text = html_entity_decode($html, ENT_HTML5);
        if ($text) {
            $layoutDocument = new \DOMDocument('1.0', 'utf-8');
            $this->loadHtml($layoutDocument, $text);

            $articleElement = $layoutDocument->getElementsByTagName('article')->item(0);
            $dataTemplateAttribute = $articleElement->attributes->getNamedItem("data-template");
            $template = $dataTemplateAttribute->textContent ?? "defaulttemplate";
            $headlineElement = $layoutDocument->getElementsByTagName('headline')->item(0);
            $perexElement = $layoutDocument->getElementsByTagName('perex')->item(0);
            if($this->paperRepo->getByReference($menuItemId)){
                user_error("Zadaná položka menu již ma připojen článek (paper).", E_USER_WARNING);
                $this->addFlashMessage("Zadaná položka menu již ma připojen článek (paper).");
            } else {
                if ($headlineElement AND $perexElement) {
                    $paper = new Paper();
                    $editor = $this->statusSecurityRepo->get()->getLoginAggregate()->getLoginName();
                    $paper
                        ->setEditor($editor)
                        ->setHeadline($this->getInnerHtml($layoutDocument, $headlineElement->childNodes))
                        ->setMenuItemIdFk($menuItemId)
                        ->setPerex($this->getInnerHtml($layoutDocument, $perexElement->childNodes))
                        ->setTemplate($template)
                        ;

                    $this->paperRepo->add($paper);
                } else {
                    $this->addFlashMessage("Selhalo vyvoření článku z šablony. Nenalezen headline a perex element."); //"Paper creating failed. No healine or perex element detected.");
                }
            }
        } else {
                                $paper = new Paper();
                    $editor = $this->statusSecurityRepo->get()->getLoginAggregate()->getLoginName();
                    $paper
                        ->setEditor($editor)
                        ->setMenuItemIdFk($menuItemId)
                        ;

                    $this->paperRepo->add($paper);
                    $this->addFlashMessage("Nebyl přijat žádný obsah šablony. Vytvořen nový prázdný článek.");
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

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

    private function loadXml($layoutDocument, $text) {
        $layoutDocument->loadXML(
"<?xml version=\"1.0\" encoding=\"utf-8\"?>
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

    private function getInnerHtml(\DOMDocument $document, \DOMNodeList $nodeList) {
        $html = "";
        foreach ($nodeList as $node) {
            /** @var \DOMNode $node */
            $html .= $document->saveHTML($node);

        }
        return $html;
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @param type $paperId
     * @return ResponseInterface
     */
    public function updateHeadline(ServerRequestInterface $request, $paperId): ResponseInterface {
        $paper = $this->paperRepo->get($paperId);
        if (!isset($paper)) {
            user_error("Neexistuje paper se zadaným id.$paperId");
        } else {
            $postHeadline = (new RequestParams())->getParam($request, 'headline_'.$paperId);
            $paper->setHeadline($postHeadline);
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @param type $paperId
     * @return ResponseInterface
     */
    public function updatePerex(ServerRequestInterface $request, $paperId): ResponseInterface {
        $paper = $this->paperRepo->get($paperId);
        if (!isset($paper)) {
            user_error("Neexistuje paper se zadaným id.$paperId");
        } else {
            $postPerex = (new RequestParams())->getParam($request, 'perex_'.$paperId);
            $paper->setPerex($postPerex);
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @param type $paperId
     * @return ResponseInterface
     */
    public function updateTemplate(ServerRequestInterface $request, $paperId): ResponseInterface {
        $paper = $this->paperRepo->get($paperId);
        if (!isset($paper)) {
            user_error("Neexistuje paper se zadaným id.$paperId");
        } else {
            $postTemplate = (new RequestParams())->getParam($request, 'folder_'.$paperId, 'default');
            $paper->setTemplate($postTemplate);
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }
}