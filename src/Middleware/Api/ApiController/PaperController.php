<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Middleware\Api\ApiController;

use Controller\PresentationFrontControllerAbstract;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Application\AppFactory;
use Pes\Http\Request\RequestParams;
use Pes\Http\Response;
use Pes\Http\Response\RedirectResponse;

use Model\Repository\{
    StatusSecurityRepo, StatusPresentationRepo, PaperRepo
};
use Model\Entity\PaperInterface;
use Model\Entity\Paper;

/**
 * Description of PostController
 *
 * @author pes2704
 */
class PaperController extends PresentationFrontControllerAbstract {

    private $paperRepo;

    public function __construct(StatusSecurityRepo $statusSecurityRepo, StatusPresentationRepo $statusPresentationRepo, PaperRepo $paperRepo) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo);
        $this->paperRepo = $paperRepo;
    }

    /**
     *
     * @param type $id mnu_item_id_fk - primární klíč paper
     * @return type
     */
    public function update(ServerRequestInterface $request, $menuItemId) {
        $paper = $this->paperRepo->get($menuItemId);
        if (!isset($paper)) {
            $paper = $this->createPaper($menuItemId);
            $this->paperRepo->add($paper);
        }
        // TODO: zjisti, jestli je třeba skládat jména proměnný - nestačí rest parametr? (může být v jednom POSTu vícekrát content a headline? - tady by se stejně použil jen jeden
        $postContent = (new RequestParams())->getParam($request, 'content_'.$menuItemId);  // jméno POST proměnné je vytvořeno v paper rendereru složením 'content_' a $paper->getMenuItemId()
        $postHeadline = (new RequestParams())->getParam($request, 'headline_'.$menuItemId);  // jméno POST proměnné je vytvořeno v paper rendereru složením 'headline_' a $paper->getMenuItemId()
        $paper->setContent($postContent)->setHeadline($postHeadline);
        return RedirectResponse::withPostRedirectGet(new Response(), $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().'www/last/'); // 303 See Other
    }

    private function createPaper($menuItemId) {
        return (new Paper())->setMenuItemIdFk($menuItemId)->setLangCode($this->statusPresentation->getLanguage()->getLangCode());
    }
}
