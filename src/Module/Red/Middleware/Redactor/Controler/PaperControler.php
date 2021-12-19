<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Middleware\Redactor\Controler;

use FrontControler\FrontControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Application\AppFactory;
use Pes\Http\Request\RequestParams;
use Pes\Http\Response;
use Pes\Http\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;


use Red\Model\Entity\Paper;
use Red\Model\Entity\PaperAggregatePaperContentInterface;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Red\Model\Repository\PaperAggregateContentsRepo;

use UnexpectedValueException;

/**
 * Description of PostControler
 *
 * @author pes2704
 */
class PaperControler extends FrontControlerAbstract {

    private $paperAggregateRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            PaperAggregateContentsRepo $paperAggregateRepo) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->paperAggregateRepo = $paperAggregateRepo;
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
            if($this->paperAggregateRepo->getByReference($menuItemId)){
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

                    $this->paperAggregateRepo->add($paper);
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

                    $this->paperAggregateRepo->add($paper);
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
    public function update(ServerRequestInterface $request, $paperId): ResponseInterface {
        /** @var PaperAggregatePaperContentInterface $paperAggregate */
        $paperAggregate = $this->paperAggregateRepo->get($paperId);
        if (!isset($paperAggregate)) {
            user_error("Neexistuje paper se zadaným id.$paperId");
        } else {
            $postParams = $request->getParsedBody();
            // jméno POST proměnné je vytvořeno v paper rendereru složením 'headline_' nebo 'perex_' nebo 'content_' a $paper->getId()
            // jiné POST parametry nepoužije (pokud jsou renderovány elementy input např. z inputů show a hide - takové parametry musí vyhodnocovat jiná action metoda, t.j. musí se odesílat spolu
            // s jiným formaction (jiné REST uri))
            if (array_key_exists("template_$paperId", $postParams)) {
                $paperAggregate->setHeadline($postParams["template_$paperId"]);
                $this->addFlashMessage('Template updated');
            }
            if (array_key_exists("headline_$paperId", $postParams)) {
                $paperAggregate->setHeadline($postParams["headline_$paperId"]);
                $this->addFlashMessage('Headline updated');
            }
            if (array_key_exists("perex_$paperId", $postParams)) {
                $paperAggregate->setPerex($postParams["perex_$paperId"]);
                $this->addFlashMessage('Perex updated');
            }
            foreach ($paperAggregate->getPaperContentsArray() as $content) {
                if (array_key_exists("content_{$content->getId()}", $postParams)) {
                    $content->setContent($postParams["content_{$content->getId()}"]);
                    $this->addFlashMessage('Content updated');
                }
            }
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @param type $paperId
     * @return ResponseInterface
     */
    public function updateHeadline(ServerRequestInterface $request, $paperId): ResponseInterface {
        /** @var PaperAggregatePaperContentInterface $paperAggregate */
        $paperAggregate = $this->paperAggregateRepo->get($paperId);
        if (!isset($paperAggregate)) {
            user_error("Neexistuje paper se zadaným id.$paperId");
        } else {
            $postParams = $request->getParsedBody();
                $paperAggregate->setHeadline($postParams["headline_$paperId"]);
                $this->addFlashMessage('Headline updated');
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
        /** @var PaperAggregatePaperContentInterface $paperAggregate */
        $paperAggregate = $this->paperAggregateRepo->get($paperId);
        if (!isset($paperAggregate)) {
            user_error("Neexistuje paper se zadaným id.$paperId");
        } else {
            $postParams = $request->getParsedBody();
            $paperAggregate->setPerex($postParams["perex_$paperId"]);
            $this->addFlashMessage('Perex updated');
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @param type $paperId
     * @return ResponseInterface
     */
    public function updateContent(ServerRequestInterface $request, $paperId, $contentId): ResponseInterface {
        /** @var PaperAggregatePaperContentInterface $paperAggregate */
        $paperAggregate = $this->paperAggregateRepo->get($paperId);
        if (!isset($paperAggregate)) {
            user_error("Neexistuje paper se zadaným id.$paperId");
        } else {
            $content = $paperAggregate->getPaperContent($contentId);
            $postParams = $request->getParsedBody();
            $content->setContent($postParams["content_{$content->getId()}"]);
            $this->addFlashMessage('Content updated');
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @param type $paperId
     * @return ResponseInterface
     */
    public function template(ServerRequestInterface $request, $paperId): ResponseInterface {
        $paper = $this->paperAggregateRepo->get($paperId);
        if (!isset($paper)) {
            user_error("Neexistuje paper se zadaným id.$paperId");
        } else {
            $postTemplate = (new RequestParams())->getParam($request, 'template_'.$paperId, 'default');

            $statusPresentation = $this->statusPresentationRepo->get();
            $templateName = $statusPresentation->getLastTemplateName();
            if (isset($templateName) AND $templateName) {
                $statusPresentation->setLastTemplateName('');
                $paper->setTemplate($templateName);
                $this->addFlashMessage("Set paper template: $templateName");
            }
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }
}
