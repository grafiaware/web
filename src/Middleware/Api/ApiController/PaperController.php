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

use Model\Repository\PaperRepo;
use Model\Entity\PaperInterface;
use Model\Entity\Paper;

/**
 * Description of PostController
 *
 * @author pes2704
 */
class PaperController extends PresentationFrontControllerAbstract {

    /**
     * Vrací pole dvojic jméno akce => role
     * @return array
     */
    public function getGrants() {
        return [
        'update'=>'authenticated',
        ];
    }

    /**
     *
     * @param type $id mnu_item_id_fk - primární klíč paper
     * @return type
     */
    public function update(ServerRequestInterface $request, $menuItemId) {
        if ($this->isPermittedMethod(__METHOD__)) {
            /* @var $paperRepo PaperRepo */
            $paperRepo = $this->container->get(PaperRepo::class);
            /* @var $paper PaperInterface */
            $paper = $paperRepo->get($menuItemId);
            if (!isset($paper)) {
                $paper = $this->createPaper($menuItemId);
                $paperRepo->add($paper);
            }
            $postContent = (new RequestParams())->getParam($this->request, 'content_'.$menuItemId);  // jméno POST proměnné je vytvořeno v paper rendereru složením 'content_' a $paper->getMenuItemId()
            $postHeadline = (new RequestParams())->getParam($this->request, 'headline_'.$menuItemId);  // jméno POST proměnné je vytvořeno v paper rendereru složením 'headline_' a $paper->getMenuItemId()
            $paper->setContent($postContent)->setHeadline($postHeadline);
        }
        return RedirectResponse::withPostRedirectGet(new Response(), $this->request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().'www/last/'); // 303 See Other
    }

    private function createPaper(ServerRequestInterface $request, $menuItemId) {
        return (new Paper())->setMenuItemIdFk($menuItemId)->setLangCode($this->statusSecurityModel->getPresentationStatus()->getLanguage());
    }
}
