<?php

namespace Middleware\Api\ApiController;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Application\AppFactory;
use Pes\Http\Response\RedirectResponse;
use Pes\Http\Response;

use Database\Hierarchy\EditHierarchy;

use Model\Repository\MenuRootRepo;
/**
 * Description of Controller
 *
 * @author pes2704
 */
class HierarchyController extends PresentationFrontControllerAbstract {

    /**
     * Vrací pole dvojic jméno akce => role
     * @return array
     */
    public function getGrants() {
        return [
        'add'=>'authenticated',
        'addchild'=>'authenticated',
        'delete'=>'supervisor',
        'trash'=>'authenticated',
        'time'=>'authenticated',
        'post'=>'authenticated',
        ];
    }

    //TODO: Svoboda Konfigurace
    const TRASH_MENU_ROOT = "trash";

    //TODO: Svoboda - addChild a add - vytvořit nový paper nebo slot
    //TODO: Svoboda - neumožnit přidávání do koše?
    //TODO: Svoboda - delete - smazat také paper nebo slot, get request na předchůdce (rodiče)
    //TODO: Svoboda - trash - get request na předchůdce (rodiče)


/* non REST metody */
    public function add(ServerRequestInterface $request, $uid) {
        if ($this->isPermittedMethod(__METHOD__)) {
            /* @var $hierarchy EditHierarchy */
            $hierarchy = $this->container->get(EditHierarchy::class);
            $siblingUid = $hierarchy->addNode($uid);
        }
        return RedirectResponse::withPostRedirectGet(new Response(), $this->request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath()."www/item/$siblingUid/"); // 303 See Other
    }

    public function addchild(ServerRequestInterface $request, $uid) {
        if ($this->isPermittedMethod(__METHOD__)) {
            /* @var $hierarchy EditHierarchy */
            $hierarchy = $this->container->get(EditHierarchy::class);
            $childUid = $hierarchy->addChildNode($uid);
        }
        return RedirectResponse::withPostRedirectGet(new Response(), $this->request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath()."www/item/$childUid/"); // 303 See Other
    }

    public function delete(ServerRequestInterface $request, $uid) {
        if ($this->isPermittedMethod(__METHOD__)) {
            /* @var $editHierarchy EditHierarchy */
            $editHierarchy = $this->container->get(EditHierarchy::class);
            $parentUid = $editHierarchy->deleteLeafNode($uid);
            return RedirectResponse::withPostRedirectGet(new Response(), $this->request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath()."www/item/$parentUid/"); // 303 See Other
        } else {
            return RedirectResponse::withPostRedirectGet(new Response(), $this->request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath()."www/item/$uid/"); // 303 See Other
        }
    }


     public function trash(ServerRequestInterface $request, $uid) {
        if ($this->isPermittedMethod(__METHOD__)) {
            /* @var $editHierarchy EditHierarchy */
            $editHierarchy = $this->container->get(EditHierarchy::class);
            /** @var MenuRootRepo $menuRootRepo */
            $menuRootRepo = $this->container->get(MenuRootRepo::class);
            $trashUid = $menuRootRepo->get(self::TRASH_MENU_ROOT)->getUidFk();
            $editHierarchy->moveSubTree($uid, $trashUid);
        }
        return RedirectResponse::withPostRedirectGet(new Response(), $this->request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().'www/last/'); // 303 See Other
     }

     public function time(ServerRequestInterface $request, $id) {

     }
}