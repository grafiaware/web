<?php

namespace Red\Middleware\Redactor\Controler;

use FrontControler\FrontControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;

use Red\Model\Dao\Hierarchy\HierarchyAggregateEditDao;
use Red\Model\Repository\MenuRootRepo;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Enum\FlashSeverityEnum;

use LogicException;
/**
 * Description of Controler
 *
 * @author pes2704
 */
class HierarchyControler extends FrontControlerAbstract {

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
        $this->addFlashMessage('add item as sibling', FlashSeverityEnum::SUCCESS);
        return $this->createResponseRedirectSeeOther($request, "web/v1/page/item/$siblingUid");
    }

    public function addchild(ServerRequestInterface $request, $uid) {
        $childUid = $this->editHierarchyDao->addChildNode($uid);
        $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
        $this->addFlashMessage('add item as child', FlashSeverityEnum::SUCCESS);
        return $this->createResponseRedirectSeeOther($request, "web/v1/page/item/$childUid");
    }

    public function cut(ServerRequestInterface $request, $uid) {
        $statusFlash = $this->statusFlashRepo->get();
        $statusFlash->setPostCommand(['cut'=>$uid]);  // command s životností do dalšího POST requestu
        $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
        $statusFlash->setMessage("cut - item: $langCode/$uid selected for cut&paste operation", FlashSeverityEnum::INFO);

        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    public function copy(ServerRequestInterface $request, $uid) {
        $statusFlash = $this->statusFlashRepo->get();
        $statusFlash->setPostCommand(['copy'=>$uid]);  // command s životností do dalšího POST requestu
        $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
        $statusFlash->setMessage("copy - item: $langCode/$uid selected for copy&paste operation", FlashSeverityEnum::INFO);

        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    public function cutEscape(ServerRequestInterface $request, $uid) {
        $statusFlash = $this->statusFlashRepo->get();
        $statusFlash->setPostCommand(null);  // zrušení výběru položky "cut"
        $statusFlash->setMessage("cut escape - operation cut&paste aborted", FlashSeverityEnum::WARNING);

        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    public function paste(ServerRequestInterface $request, $uid) {
        $parentUid = $this->editHierarchyDao->getParentNode($uid)['uid'];
        $statusFlash = $this->statusFlashRepo->get();
        $success = false;
        if (isset($parentUid)) {
            $postCommand = $statusFlash->getPostCommand();
            if (is_array($postCommand) ) {
                $key = array_key_first($postCommand);
                $pasteduid = $postCommand[$key];
                switch ($key) {
                    case 'cut':
                        $this->editHierarchyDao->moveSubTreeAsSiebling($pasteduid, $uid);
                        $this->addFlashMessage('cut items pasted as a siblings', FlashSeverityEnum::SUCCESS);
                        $success = true;
                        break;
                    case 'copy':
                        $this->editHierarchyDao->copySubTreeAsSiebling($pasteduid, $uid);
                        $this->addFlashMessage('copied items pasted as a siblings', FlashSeverityEnum::SUCCESS);
                        $success = true;
                        break;
                    default:
                        $this->addFlashMessage("Unknown post command.", FlashSeverityEnum::WARNING);
                        break;
                }
            }else {
                $this->addFlashMessage("No post command.", FlashSeverityEnum::WARNING);
            }
        } else {
            $this->addFlashMessage('unable to paste, item has no parent', FlashSeverityEnum::WARNING);
        }
        return $success ? $this->createResponseRedirectSeeOther($request, "web/v1/page/item/$pasteduid") : $this->createResponseRedirectSeeOther($request, "web/v1/page/item/$uid");
    }

    public function pastechild(ServerRequestInterface $request, $uid) {
        $statusFlash = $this->statusFlashRepo->get();

        $statusFlash = $this->statusFlashRepo->get();
        $success = false;
        $postCommand = $statusFlash->getPostCommand();
        if (is_array($postCommand) ) {
            $key = array_key_first($postCommand);
            $pasteduid = $postCommand[$key];
            switch ($key) {
                case 'cut':
                    $this->editHierarchyDao->moveSubTreeAsChild($pasteduid, $uid);
                    $this->addFlashMessage('cut items pasted as a child', FlashSeverityEnum::SUCCESS);
                    $success = true;
                    break;
                case 'copy':
                    $this->editHierarchyDao->copySubTreeAsChild($pasteduid, $uid);
                    $this->addFlashMessage('copied items pasted as a child', FlashSeverityEnum::SUCCESS);
                    $success = true;
                    break;
                default:
                    $this->addFlashMessage("Unknown post command.", FlashSeverityEnum::WARNING);
                    break;
            }
        }else {
            $this->addFlashMessage("No post command.", FlashSeverityEnum::WARNING);
        }
        return $success ? $this->createResponseRedirectSeeOther($request, "web/v1/page/item/$pasteduid") : $this->createResponseRedirectSeeOther($request, "web/v1/page/item/$uid");
    }

    public function delete(ServerRequestInterface $request, $uid) {
        // opatrná varianta - maže jen leaf - nutno mazat po jednom uzlu (to se musí projevit i v renderování obládacích prvků - tlačítek, nabízet smazat jen pro leaf)
//        $parentUid = $this->editHierarchyDao->deleteLeafNode($uid);
        $parentUid = $this->editHierarchyDao->deleteSubTree($uid);
        $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
        $this->addFlashMessage('delete', FlashSeverityEnum::SUCCESS);
        return $this->createResponseRedirectSeeOther($request, "web/v1/page/item/$parentUid");
    }

    // odloženo!!
    public function nonPermittedDelete(ServerRequestInterface $request, $uid) {
        $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
        return $this->createResponseRedirectSeeOther($request, "web/v1/page/item/$uid");
    }


    public function trash(ServerRequestInterface $request, $uid) {
        $trashUid = $this->menuRootRepo->get(self::TRASH_MENU_ROOT)->getUidFk();
        $this->editHierarchyDao->moveSubTreeAsChild($uid, $trashUid);
        $this->addFlashMessage('trash', FlashSeverityEnum::SUCCESS);
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

}