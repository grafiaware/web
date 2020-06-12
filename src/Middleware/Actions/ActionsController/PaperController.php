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

use Model\Repository\{
    StatusSecurityRepo, StatusFlashRepo, StatusPresentationRepo, MenuItemAggregateRepo
};
use Model\Entity\MenuItemPaperAggregateInterface;
use Model\Entity\MenuItemPaperAggregate;

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
            MenuItemAggregateRepo $paperRepo) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->paperRepo = $paperRepo;
    }

    /**
     *
     * @param type $id mnu_item_id_fk - primární klíč paper
     * @return type
     */
    public function updateHeadline(ServerRequestInterface $request, $menuItemId) {
        $paper = $this->paperRepo->get($menuItemId);
        if (!isset($paper)) {
            user_error('Neexistuje paper se zadaným menuItemId.');
        } else {
            $postHeadline = (new RequestParams())->getParam($request, 'headline_'.$menuItemId);
            $paper->getPaperHeadline()->setHeadline($postHeadline);
        }
        return RedirectResponse::withPostRedirectGet(new Response(), $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().'www/last/'); // 303 See Other
    }

    /**
     *
     * @param type $id mnu_item_id_fk - primární klíč paper
     * @return type
     */
    public function updateContent(ServerRequestInterface $request, $menuItemId, $id) {
        $paper = $this->paperRepo->get($menuItemId);
        if (!isset($paper)) {
            user_error('Neexistuje paper se zadaným menuItemId.');
        } else {
            $postContent = (new RequestParams())->getParam($request, 'content_'.$id);  // jméno POST proměnné je vytvořeno v paper rendereru složením 'content_' a $paper->getMenuItemId()

            $content = $paper->getPaperContent($id);
            $content->setContent($postContent);
        }

        return RedirectResponse::withPostRedirectGet(new Response(), $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().'www/last/'); // 303 See Other
    }

//    $paper = $this->paperRepo->get($menuItemId) ?? $this->createPaper($menuItemId);

//    private function createPaper($menuItemId) {
//        $paper = (new Paper())->setMenuItemIdFk($menuItemId)->setLangCode($this->statusPresentation->getLanguage()->getLangCode());
//        $this->paperRepo->add($paper);
//        return $paper;
//    }
}
