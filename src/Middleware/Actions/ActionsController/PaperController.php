<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Middleware\Actions\ActionsController;

use Controller\PresentationFrontControllerAbstract;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Application\AppFactory;
use Pes\Http\Request\RequestParams;
use Pes\Http\Response;
use Pes\Http\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;

use Model\Repository\{
    StatusSecurityRepo, StatusFlashRepo, StatusPresentationRepo, PaperRepo
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
        $html = (new RequestParams())->getParam($request, 'paper_template');  // jméno POST proměnné je vytvořeno v paper rendereru
        $layoutDocument = new \DOMDocument('1.0');
        $layoutDocument->loadHTML(
"<!DOCTYPE html>
<!--

-->
<html>
    <head>
    </head>
    <body>
    </body>
</html>"
           );

        $bodyNode = $layoutDocument->getElementsByTagName('body')->item(0);
        $bodyNode->textContent = $html;
//        $headlineElement = $layoutDocument->getElementsByTagName('headline')->item(0);
//        $perexElement = $layoutDocument->getElementsByTagName('perex')->item(0);


        return $this->redirectSeeOther($request,'www/last/'); // 303 See Other

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
        return $this->redirectSeeOther($request,'www/last/'); // 303 See Other
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
        return $this->redirectSeeOther($request,'www/last/'); // 303 See Other
    }

//    $paper = $this->paperRepo->get($menuItemId) ?? $this->createPaper($menuItemId);

//    private function createPaper($menuItemId) {
//        $paper = (new Paper())->setMenuItemIdFk($menuItemId)->setLangCode($this->statusPresentation->getLanguage()->getLangCode());
//        $this->paperRepo->add($paper);
//        return $paper;
//    }
}
