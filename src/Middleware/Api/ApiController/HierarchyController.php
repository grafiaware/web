<?php

namespace Middleware\Api\ApiController;

use Controller\PresentationFrontControllerAbstract;

use Psr\Http\Message\ServerRequestInterface;

use Model\Dao\Hierarchy\NodeEditDao;

use Model\Repository\{
    StatusSecurityRepo, StatusFlashRepo, StatusPresentationRepo, MenuRootRepo
};

/**
 * Description of Controller
 *
 * @author pes2704
 */
class HierarchyController extends PresentationFrontControllerAbstract {

    //TODO: Svoboda Konfigurace
    const TRASH_MENU_ROOT = "trash";

    //TODO: Svoboda - addChild a add - vytvořit nový paper nebo slot
    //TODO: Svoboda - neumožnit přidávání do koše?
    //TODO: Svoboda - delete - smazat také paper nebo slot, get request na předchůdce (rodiče)
    //TODO: Svoboda - trash - get request na předchůdce (rodiče)

    private $editHierarchyDao;
    private $menuRootRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            NodeEditDao $editHierarchyDao,
            MenuRootRepo $menuRootRepo) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        // TODO: vyměnit hierarchy Dao za Repo
        $this->editHierarchyDao = $editHierarchyDao;
        $this->menuRootRepo = $menuRootRepo;
    }

/* non REST metody */
    public function add(ServerRequestInterface $request, $uid) {
        $siblingUid = $this->editHierarchyDao->addNode($uid);
        $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
        $this->addFlashMessage('add');
        return $this->redirectSeeOther($request, "www/item/$langCode/$siblingUid/");
    }

    public function addchild(ServerRequestInterface $request, $uid) {
        $childUid = $this->editHierarchyDao->addChildNode($uid);
        $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
        $this->addFlashMessage('addchild');
        return $this->redirectSeeOther($request, "www/item/$langCode/$childUid/");
    }

    public function paste(ServerRequestInterface $request, $uid, $pasteduid) {
        $parentUid = $this->editHierarchyDao->getNode($uid)['parent_uid'];
        if (isset($parentUid)) {
            $this->editHierarchyDao->moveSubTree($pasteduid, $parentUid);
            $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
            $this->addFlashMessage('paste');
        } else {
            $this->addFlashMessage('unable to paste, item has no parent');
        }
        return $this->redirectSeeOther($request, "www/item/$langCode/$pasteduid/");
    }

    public function pastechild(ServerRequestInterface $request, $uid, $pasteduid) {
        $this->editHierarchyDao->moveSubTree($pasteduid, $uid);
        $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
        $this->addFlashMessage('paste child');
        return $this->redirectSeeOther($request, "www/item/$langCode/$pasteduid/");
    }

    public function delete(ServerRequestInterface $request, $uid) {
        // opatrná varianta - maže jen leaf - nutno mazat po jednom uzlu (to se musí projevit i v renderování obládacích prvků - tlačítek, nabízet smazat jen pro leaf)
//        $parentUid = $this->editHierarchyDao->deleteLeafNode($uid);
        $parentUid = $this->editHierarchyDao->deleteSubTree($uid);
        $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
        $this->addFlashMessage('delete');
        return $this->redirectSeeOther($request, "www/item/$langCode/$parentUid/");
    }

    // odloženo!!
    public function nonPermittedDelete(ServerRequestInterface $request, $uid) {
        $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
        return $this->redirectSeeOther($request, "www/item/$langCode/$uid/");
    }


    public function trash(ServerRequestInterface $request, $uid) {
        $trashUid = $this->menuRootRepo->get(self::TRASH_MENU_ROOT)->getUidFk();
        $this->editHierarchyDao->moveSubTree($uid, $trashUid);
        $this->addFlashMessage('trash');
        return $this->redirectSeeOther($request, 'www/last/');
    }

}