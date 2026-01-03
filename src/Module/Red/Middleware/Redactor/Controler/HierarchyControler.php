<?php

namespace Red\Middleware\Redactor\Controler;

use FrontControler\FrontControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Red\Model\Dao\Hierarchy\HierarchyAggregateEditDao;
use Red\Model\Repository\MenuRootRepo;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Enum\FlashSeverityEnum;

/**
 * Description of Controler
 *
 * @author pes2704
 */
class HierarchyControler extends FrontControlerAbstract {

    //TODO: Svoboda Konfigurace
    const TRASH_MENU_ROOT = "trash";
    
    const POST_COMMAND_CUT = 'post_coomand_cut';
    const POST_COMMAND_COPY = 'post_coomand_copy';
    

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
    public function add(ServerRequestInterface $request, $uid): ResponseInterface {
        $siblingUid = $this->editHierarchyDao->addNode($uid);
        $this->addFlashMessage('add item as sibling', FlashSeverityEnum::SUCCESS);
        return $this->createJsonOKResponse(["refresh"=>"navigation", "targeturi"=> $this->getContentApiUri($siblingUid), "newitemuid"=>$siblingUid]);        
        //TODO: POST version
        return $this->createResponseRedirectSeeOther($request, "web/v1/page/item/$siblingUid");
    }

    public function addChild(ServerRequestInterface $request, $uid): ResponseInterface {
        $childUid = $this->editHierarchyDao->addChildNode($uid);
        $this->addFlashMessage('add item as child', FlashSeverityEnum::SUCCESS);
        return $this->createJsonOKResponse(["refresh"=>"navigation", "targeturi"=> $this->getContentApiUri($childUid), "newitemuid"=>$childUid]);
        //TODO: POST version
        return $this->createResponseRedirectSeeOther($request, "web/v1/page/item/$childUid");
    }

    public function cut(ServerRequestInterface $request, $uid): ResponseInterface {
        $statusFlash = $this->statusFlashRepo->get();
        $statusFlash->setPostCommand([self::POST_COMMAND_CUT=>$uid]);  // command s životností do dalšího POST requestu
        $langCode = $this->statusPresentationRepo->get()->getLanguageCode();
        $statusFlash->setMessage("cut - item: $langCode/$uid selected for cut&paste operation", FlashSeverityEnum::INFO);
        return $this->createJsonOKResponse(["refresh"=>"item", "newitemuid"=>$uid]);  // refresh jen driver
        //TODO: POST version
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    public function copy(ServerRequestInterface $request, $uid): ResponseInterface {
        $statusFlash = $this->statusFlashRepo->get();
        $statusFlash->setPostCommand([self::POST_COMMAND_COPY=>$uid]);  // command s životností do dalšího POST requestu
        $langCode = $this->statusPresentationRepo->get()->getLanguageCode();
        $statusFlash->setMessage("copy - item: $langCode/$uid selected for copy&paste operation", FlashSeverityEnum::INFO);
        return $this->createJsonOKResponse(["refresh"=>"item", "newitemuid"=>$uid]);  // refresh jen driver
        //TODO: POST version
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    public function cutEscape(ServerRequestInterface $request, $uid): ResponseInterface {
        $statusFlash = $this->statusFlashRepo->get();
        $statusFlash->getPostCommand();  // zrušení výběru položky "cut"
        $statusFlash->setMessage("cut escape - operation cut&paste aborted", FlashSeverityEnum::WARNING);
        return $this->createJsonOKResponse(["refresh"=>"item", "newitemuid"=>$uid]);  // refresh jen driver
        //TODO: POST version
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
    public function paste(ServerRequestInterface $request, $uid): ResponseInterface {
        $parentNode = $this->editHierarchyDao->getParentNodeHelper($uid);  // vrací jen node - bez položky menu
        $statusFlash = $this->statusFlashRepo->get();
        $success = false;
        if (isset($parentNode)) {
            $postCommand = $statusFlash->getPostCommand();
            if (is_array($postCommand) ) {
                $command = array_key_first($postCommand);
                $pasteduid = $postCommand[$command];
                switch ($command) {
                    case self::POST_COMMAND_CUT:
                        $this->editHierarchyDao->moveSubTreeAsSiebling($pasteduid, $uid, false);  // vypnuta deaktivace 
                        $this->addFlashMessage('Cutted items inserted as siblings.', FlashSeverityEnum::SUCCESS);
                        $success = true;
                        break;
                    case self::POST_COMMAND_COPY:
                        //TODO: Dodělat transformaci obsahů po kopírování podstromu menu
                        $transform = $this->editHierarchyDao->copySubTreeAsSiebling($pasteduid, $uid, false);  // vypnuta deaktivace 
                        $this->addFlashMessage('Copied items inserted as siblings.', FlashSeverityEnum::SUCCESS);
                        $success = true;
                        break;
                    default:
                        $this->addFlashMessage("Paste - unknown post command.", FlashSeverityEnum::WARNING);
                        break;
                }
            }else {
                $this->addFlashMessage("No post command.", FlashSeverityEnum::WARNING);
            }
        } else {
            $this->addFlashMessage('Unable to paste as siebling, item has no parent.', FlashSeverityEnum::WARNING);
        }
//        if ($success ) {
            return $this->createJsonOKResponse(["refresh"=>"navigation", "targeturi"=> $this->getContentApiUri($pasteduid), "newitemuid"=>$pasteduid]);
//        } else {
            return $this->createJsonOKResponse(["refresh"=>"navigation", "newitemuid"=>$uid]);  // refresh jen driver
//        }
        //TODO: POST version
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
    public function pasteChild(ServerRequestInterface $request, $uid): ResponseInterface {
        $statusFlash = $this->statusFlashRepo->get();
        $success = false;
        $postCommand = $statusFlash->getPostCommand();
        if (is_array($postCommand) ) {
            $command = array_key_first($postCommand);
            $pasteduid = $postCommand[$command];
            switch ($command) {
                case self::POST_COMMAND_CUT:
                    $this->editHierarchyDao->moveSubTreeAsChild($pasteduid, $uid, false);  // vypnuta deaktivace 
                    $this->addFlashMessage('Cutted items inserted as children.', FlashSeverityEnum::SUCCESS);
                    $success = true;
                    break;
                case self::POST_COMMAND_COPY:
                    $transform = $this->editHierarchyDao->copySubTreeAsChild($pasteduid, $uid, false);  // vypnuta deaktivace 
                    $this->addFlashMessage('Copied items inserted as children.', FlashSeverityEnum::SUCCESS);
                    $success = true;
                    break;
                default:
                    $this->addFlashMessage("Paste child - unknown post command.", FlashSeverityEnum::WARNING);
                    break;
            }
        }else {
            $this->addFlashMessage("No post command.", FlashSeverityEnum::WARNING);
        }
//        if ($success ) {
            return $this->createJsonOKResponse(["refresh"=>"navigation", "targeturi"=> $this->getContentApiUri($pasteduid), "newitemuid"=>$pasteduid]);
//        } else {
            return $this->createJsonOKResponse(["refresh"=>"navigation", "targeturi"=> $this->getContentApiUri($uid), "newitemuid"=>$uid]);
//        }
        //TODO: POST version
        return $success ? $this->createResponseRedirectSeeOther($request, "web/v1/page/item/$pasteduid") : $this->createResponseRedirectSeeOther($request, "web/v1/page/item/$uid");
    }

    /**
     * Smaže z databáze (nevratně pomocí DELETE) položku menu a podřízené záznamy v dalších tabulkách
     *  - maže hierarchy pomocí DAO, hooked actor v metodě delete smaže menu_item a případný menu_item_asset a asset (nemaže soubory "assets") a menu_root
     *  - následně díky cizím klíčům s constraint On delete: CASCADE dojde i ke smazání řádku v article nebo paper včetně sections nebo multipage nebo static
     * Metoda smaže všechny jazykové verze (vybírá menu itemy jen podle uid).
     * 
     * @param ServerRequestInterface $request
     * @param type $uid
     * @return ResponseInterface
     */
    public function delete(ServerRequestInterface $request, $uid): ResponseInterface {
        $parentNode = $this->editHierarchyDao->getParentNodeHelper($uid);
        $this->editHierarchyDao->deleteSubTree($uid);
        $this->addFlashMessage('delete', FlashSeverityEnum::SUCCESS);
        $redirectUid = $parentNode['uid'];   // kořen trash
            return $this->createJsonOKResponse(["refresh"=>"navigation", "targeturi"=> $this->getContentApiUri($redirectUid), "newitemuid"=>$redirectUid]);
        //TODO: POST version
        return $this->createResponseRedirectSeeOther($request, "web/v1/page/item/$redirectUid");
    }

    // odloženo!!
    public function nonPermittedDelete(ServerRequestInterface $request, $uid): ResponseInterface {
        return $this->createJsonOKResponse(["refresh"=>"closest"]);
        //TODO: POST version
        return $this->createResponseRedirectSeeOther($request, "web/v1/page/item/$uid");
    }


    public function trash(ServerRequestInterface $request, $uid): ResponseInterface {
        $parentNode = $this->editHierarchyDao->getParentNodeHelper($uid);
        $trashUid = $this->menuRootRepo->get(self::TRASH_MENU_ROOT)->getUidFk();
        $this->editHierarchyDao->moveSubTreeAsChild($uid, $trashUid);
        $this->addFlashMessage('trash', FlashSeverityEnum::SUCCESS);
        $redirectUid = isset($parentNode) ? $parentNode['uid'] : $uid;
        // ještě přepnout item (switchItem) - 
        // <a href="web/v1/page/item/664230b8de0c0" data-red-content="red/v1/paper/28" data-red-driver="red/v1/presenteddriver/664230b8de0c0"><span>Katalog umělců a institucí 2023</span><span class="semafor"><i class="circle icon green" title="published"></i></span></a>
                       return $this->createJsonOKResponse(["refresh"=>"navigation", "targeturi"=> $this->getContentApiUri($redirectUid), "newitemuid"=>$redirectUid]);
        //TODO: POST version        
        return $this->createResponseRedirectSeeOther($request, "web/v1/page/item/$redirectUid");
    }

    private function getContentApiUri($uid) {
        $langCode = $this->statusPresentationRepo->get()->getLanguageCode();
        $node = $this->editHierarchyDao->get(["lang_code_fk"=>$langCode, "uid_fk"=>$uid]);
        if (!isset($node)) {
            throw new Exception;
        }
        return "/red/v1/{$node['api_generator_fk']}/{$node['id']}";
    }
    
}