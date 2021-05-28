<?php

namespace Red\Middleware\Redactor\Controller;

use FrontController\PresentationFrontControllerAbstract;

use Psr\Http\Message\ServerRequestInterface;

use Red\Model\Dao\Hierarchy\HierarchyAggregateEditDao;

use Status\Model\Repository\{
    StatusSecurityRepo, StatusFlashRepo, StatusPresentationRepo
};
use Red\Model\Repository\{
    MenuRootRepo
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
            HierarchyAggregateEditDao $editHierarchyDao,
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
        $this->addFlashMessage('add item as sibling');
        return $this->redirectSeeOther($request, "web/v1/page/item/$siblingUid");
    }

    public function addchild(ServerRequestInterface $request, $uid) {
        $childUid = $this->editHierarchyDao->addChildNode($uid);
        $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
        $this->addFlashMessage('add item as child');
        return $this->redirectSeeOther($request, "web/v1/page/item/$childUid");
    }

    public function cut(ServerRequestInterface $request, $uid) {
        $statusFlash = $this->statusFlashRepo->get();
        $statusFlash->setPostCommand(['cut'=>$uid]);  // command s životností do dalšího POST requestu
        $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
        $statusFlash->appendMessage("cut - item: $langCode/$uid selected for cut&patse operation");

        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    public function cutEscape(ServerRequestInterface $request, $uid) {
        $statusFlash = $this->statusFlashRepo->get();
        $statusFlash->setPostCommand(null);  // zrušení výběru položky "cut"
        $statusFlash->appendMessage("cut escape -cut&patse operation aborted");

        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    public function paste(ServerRequestInterface $request, $uid) {
        $parentUid = $this->editHierarchyDao->getNode($uid)['parent_uid'];
        $statusFlash = $this->statusFlashRepo->get();
        $pasteduid = $statusFlash->getPostCommand()['cut'];  // command s životností do dalšího POST requestu
        if (isset($parentUid)) {
            $this->editHierarchyDao->moveSubTreeAsSiebling($pasteduid, $uid);
            $this->addFlashMessage('pasted as a sibling');
        } else {
            $this->addFlashMessage('unable to paste, item has no parent');
        }
        $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
        return $this->redirectSeeOther($request, "web/v1/page/item/$pasteduid");
    }

    public function pastechild(ServerRequestInterface $request, $uid) {
        $statusFlash = $this->statusFlashRepo->get();
        $pasteduid = $statusFlash->getPostCommand()['cut'];  // command s životností do dalšího POST requestu
        $this->editHierarchyDao->moveSubTreeAsChild($pasteduid, $uid);
        $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
        $this->addFlashMessage('pasted as a child');
        return $this->redirectSeeOther($request, "web/v1/page/item/$pasteduid");
    }

    public function delete(ServerRequestInterface $request, $uid) {
        // opatrná varianta - maže jen leaf - nutno mazat po jednom uzlu (to se musí projevit i v renderování obládacích prvků - tlačítek, nabízet smazat jen pro leaf)
//        $parentUid = $this->editHierarchyDao->deleteLeafNode($uid);
        $parentUid = $this->editHierarchyDao->deleteSubTree($uid);
        $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
        $this->addFlashMessage('delete');
        return $this->redirectSeeOther($request, "web/v1/page/item/$parentUid");
    }

    // odloženo!!
    public function nonPermittedDelete(ServerRequestInterface $request, $uid) {
        $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
        return $this->redirectSeeOther($request, "web/v1/page/item/$uid");
    }


    public function trash(ServerRequestInterface $request, $uid) {
        $trashUid = $this->menuRootRepo->get(self::TRASH_MENU_ROOT)->getUidFk();
        $this->editHierarchyDao->moveSubTreeAsChild($uid, $trashUid);
        $this->addFlashMessage('trash');
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

}