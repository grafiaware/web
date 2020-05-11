<?php

namespace Middleware\Api\ApiController;

use Controller\PresentationFrontControllerAbstract;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Application\AppFactory;
use Pes\Http\Response\RedirectResponse;
use Pes\Http\Response;

use Database\Hierarchy\EditHierarchy;

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
            EditHierarchy $editHierarchyDao,
            MenuRootRepo $menuRootRepo) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        // TODO: vyměnit hierarchy Dao za Repo
        $this->editHierarchyDao = $editHierarchyDao;
        $this->menuRootRepo = $menuRootRepo;
    }

/* non REST metody */
    public function add(ServerRequestInterface $request, $uid) {
        $siblingUid = $this->editHierarchyDao->addNode($uid);
        return $this->redirectSeeOther($request, "www/item/$siblingUid/");
    }

    public function addchild(ServerRequestInterface $request, $uid) {
        $childUid = $this->editHierarchyDao->addChildNode($uid);
        return $this->redirectSeeOther($request, "www/item/$childUid/");
    }

    public function delete(ServerRequestInterface $request, $uid) {
        $parentUid = $this->editHierarchyDao->deleteLeafNode($uid);
        return $this->redirectSeeOther($request, "www/item/$parentUid/");
    }

    // odloženo!!
    public function nonPermittedDelete(ServerRequestInterface $request, $uid) {

        return $this->redirectSeeOther($request, "www/item/$uid/");
    }


    public function trash(ServerRequestInterface $request, $uid) {
        $trashUid = $this->menuRootRepo->get(self::TRASH_MENU_ROOT)->getUidFk();
        $this->editHierarchyDao->moveSubTree($uid, $trashUid);
        return $this->redirectSeeOther($request, 'www/last/');
    }

    public function time(ServerRequestInterface $request, $id) {

    }

    /**
     *
     * @param string $relativePath
     * @return Response
     */
    private function redirectSeeOther($request, $relativePath) {
        return RedirectResponse::withPostRedirectGet(new Response(), $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().$relativePath); // 303 See Other
    }
}