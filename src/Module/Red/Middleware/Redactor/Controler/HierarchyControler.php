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

    /**
     * Provede zkopírování nebo přesunutí položek menu vybraných k zkopírování nebo přesunutí. Kopírované nebo přesouvané položky umístí jako sourozence node se zadaným uid a to "za"
     * zadaný node.
     * Všechny zkopírované nebo přesouvané položky menu deaktivuje.
     * Výskyt aktivní položky mezi potomky neaktivní položky způsobí chyby při renderování stromu menu v needitačním režimu. To musí být splněno ve všech jazykových verzích.
     *
     * @param ServerRequestInterface $request
     * @param type $uid
     * @return type
     */
    public function paste(ServerRequestInterface $request, $uid) {
        $parentNode = $this->editHierarchyDao->getParentNodeHelper($uid);  // vrací jen node - bez položky menu
        $statusFlash = $this->statusFlashRepo->get();
        $success = false;
        if (isset($parentNode)) {
            $postCommand = $statusFlash->getPostCommand();
            if (is_array($postCommand) ) {
                $command = array_key_first($postCommand);
                $pasteduid = $postCommand[$command];
                switch ($command) {
                    case 'cut':
                        $this->editHierarchyDao->moveSubTreeAsSiebling($pasteduid, $uid);
                        $this->addFlashMessage('cut items pasted as a siblings', FlashSeverityEnum::SUCCESS);
                        $success = true;
                        break;
                    case 'copy':
                        $transform = $this->editHierarchyDao->copySubTreeAsSiebling($pasteduid, $uid);
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

    /**
     * Provede zkopírování nebo přesunutí položek menu vybraných k zkopírování nebo přesunutí. Kopírované nebo přesouvané položky umístí jako potomka node se zadaným uid.
     * 
     * Všechny zkopírované nebo přesouvané položky menu deaktivuje.
     * Výskyt aktivní položky mezi potomky neaktivní položky způsobí chyby při renderování stromu menu v needitačním režimu. To musí být splněno ve všech jazykových verzích.
     *
     * @param ServerRequestInterface $request
     * @param type $uid
     * @return type
     */
    public function pastechild(ServerRequestInterface $request, $uid) {
        $statusFlash = $this->statusFlashRepo->get();
        $success = false;
        $postCommand = $statusFlash->getPostCommand();
        if (is_array($postCommand) ) {
            $command = array_key_first($postCommand);
            $pasteduid = $postCommand[$command];
            switch ($command) {
                case 'cut':
                    $this->editHierarchyDao->moveSubTreeAsChild($pasteduid, $uid);
                    $this->addFlashMessage('cut items pasted as a child', FlashSeverityEnum::SUCCESS);
                    $success = true;
                    break;
                case 'copy':
                    $transform = $this->editHierarchyDao->copySubTreeAsChild($pasteduid, $uid);
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
        $parentNode = $this->editHierarchyDao->getParentNodeHelper($uid);
        $this->editHierarchyDao->deleteSubTree($uid);
        $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
        $this->addFlashMessage('delete', FlashSeverityEnum::SUCCESS);
        $redirectUid = isset($parentNode) ? $parentNode['uid'] : $uid;
        return $this->createResponseRedirectSeeOther($request, "web/v1/page/item/$redirectUid");
    }

    // odloženo!!
    public function nonPermittedDelete(ServerRequestInterface $request, $uid) {
        $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
        return $this->createResponseRedirectSeeOther($request, "web/v1/page/item/$uid");
    }


    public function trash(ServerRequestInterface $request, $uid) {
        $parentNode = $this->editHierarchyDao->getParentNodeHelper($uid);
        $trashUid = $this->menuRootRepo->get(self::TRASH_MENU_ROOT)->getUidFk();
        $this->editHierarchyDao->moveSubTreeAsChild($uid, $trashUid);
        $this->addFlashMessage('trash', FlashSeverityEnum::SUCCESS);
        $redirectUid = isset($parentNode) ? $parentNode['uid'] : $uid;
        return $this->createResponseRedirectSeeOther($request, "web/v1/page/item/$redirectUid");
    }

}