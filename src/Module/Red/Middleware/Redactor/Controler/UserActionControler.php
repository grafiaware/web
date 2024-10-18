<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Middleware\Redactor\Controler;

use FrontControler\FrontControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Application\AppFactory;
use Pes\Http\Request\RequestParams;
use Pes\Http\Response;
use Pes\Http\Response\RedirectResponse;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

use Status\Model\Enum\FlashSeverityEnum;

use Red\Model\Repository\LanguageRepo;
use Red\Model\Repository\MenuItemRepo;
use Red\Model\Repository\ItemActionRepo;
use Red\Service\ItemAction\ItemActionServiceInterface;

use Red\Model\Entity\ItemAction;
use Red\Model\Enum\AuthoredTypeEnum;

use Red\Middleware\Redactor\Controler\Exception\UnexpectedLanguageException;

/**
 * Description of PostControler
 *
 * @author pes2704
 */
class UserActionControler extends FrontControlerAbstract {

    const FORM_USER_ACTION_EDIT_MODE = 'edit_mode';
    const FORM_USER_ACTION_EDIT_MENU = 'edit_menu';
    const FORM_USER_ACTION_EDIT_CONTENT = 'edit_content';

    private $languageRepo;
    
    private $itemActionService;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            LanguageRepo $languageRepo,
            ItemActionServiceInterface $itemActionService) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->languageRepo = $languageRepo;
        $this->itemActionService = $itemActionService;
    }

    public function setLangCode(ServerRequestInterface $request) {
        $requestedLangCode = (new RequestParams())->getParsedBodyParam($request, 'langcode');
        $language = $this->languageRepo->get($requestedLangCode);
        if (isset($language)) {
            $this->statusPresentationRepo->get()->setLanguage($language);
        } else{
            throw new UnexpectedLanguageException("Požadavek a nastavení neznámého jazyka aplikace s kódem $requestedLangCode.");
        }
        $this->addFlashMessage("setLangCode({$language->getLangCode()})", FlashSeverityEnum::INFO);
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    public function setEditMode(ServerRequestInterface $request) {
        $edit = (bool) (new RequestParams())->getParsedBodyParam($request, self::FORM_USER_ACTION_EDIT_MODE);
        // SMAZÁNÍ ItemActions přihlášeného uživatele z databáze při vzpnutí editačního režimu
        if (!$edit) {
            $loginName = $this->statusSecurityRepo->get()->getLoginAggregate()->getLoginName();
            $this->itemActionService->removeUserItemActions($loginName);
        }
        // nastavení aktuálního editačního režimu ve statusu
        $this->statusSecurityRepo->get()->getEditorActions()->setEditableContent($edit);
        $this->addFlashMessage("set editable content $edit", FlashSeverityEnum::INFO);

        //TODO: nejdřív vypnu editable a pak teprve volám isPresentedItemActive() - pokud menuItem není active, tak se s vypnutým editable už v metodě isPresentedItemActive() nenačte - ?? obráceně?

        // při zapnutí editačního modu nebo při jeho vypnutí, ale pro neaktivní položku - zobrazím znovu aktuální stránku
        // při vypnutí editace a zobrazené aktivní položce - přesměruji na home, neaktivní položka v needitačním režimu není zobrazená (není publikovaná) 
        // a zobrazila by se prázdná stránka
        if ($edit OR $this->isPresentedItemActive()) {
            return $this->redirectSeeLastGet($request); // 303 See Other
        } else {
            return $this->createResponseRedirectSeeOther($request, ''); // 303 See Other -> home - jinak zůstane prezentovaný poslední segment layoutu, který nyl editován v režimu edit layout
        }
    }

    public function setEditMenu(ServerRequestInterface $request) {
        $edit = (new RequestParams())->getParsedBodyParam($request, self::FORM_USER_ACTION_EDIT_MENU);
        $this->addFlashMessage("set editable menu $edit", FlashSeverityEnum::INFO);
        $this->statusSecurityRepo->get()->getEditorActions()->setEditableMenu($edit);
//        if ($edit OR $this->isPresentedItemActive()) {
            return $this->redirectSeeLastGet($request); // 303 See Other
//        } else {
//            return $this->createResponseRedirectSeeOther($request, ''); // 303 See Other -> home - jinak zůstane prezentovaný poslední item, který byl editován v režimu edit 
//        }
    }

    private function isPresentedItemActive() {
        $statusPresentation = $this->statusPresentationRepo->get();
        $menuItem = $statusPresentation->getMenuItem();
        return $menuItem ? $menuItem->getActive() : false;
    }
}
