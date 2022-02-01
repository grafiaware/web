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

use Red\Model\Repository\LanguageRepo;
use Red\Model\Repository\MenuItemRepo;
use Red\Model\Repository\ItemActionRepo;

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

    private $menuItemRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            LanguageRepo $languageRepo,
            MenuItemRepo $menuItemRepo) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->languageRepo = $languageRepo;
        $this->menuItemRepo = $menuItemRepo;  // nevyužito - zrušena metoda setPresentedItem
    }

    public function setLangCode(ServerRequestInterface $request) {
        $requestedLangCode = (new RequestParams())->getParsedBodyParam($request, 'langcode');
        $language = $this->languageRepo->get($requestedLangCode);
        if (isset($language)) {
            $this->statusPresentationRepo->get()->setLanguage($language);
        } else{
            throw new UnexpectedLanguageException("Požadavek a nastavení neznámého jazyka aplikace s kódem $requestedLangCode.");
        }
        $this->addFlashMessage("setLangCode({$language->getLangCode()})");
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

//    public function setPresentedItem(ServerRequestInterface $request) {
//        $requestedUid = (new RequestParams())->getParsedBodyParam($request, 'uid');
//        $statusPresentation = $this->statusPresentationRepo->get();
//        $langCodeFk = $statusPresentation->getLanguage()->getLangCode();
//        $menuItem = $this->menuItemRepo->get($langCodeFk, $requestedUid);
//        $statusPresentation->setHierarchyAggregate($menuItem);  // bez kontroly
//        $this->addFlashMessage("setPresentedItem({$menuItem->getTitle()})");
//        return $this->redirectSeeLastGet($request); // 303 See Other
//    }

    public function setEditMode(ServerRequestInterface $request) {
        $edit = (new RequestParams())->getParsedBodyParam($request, self::FORM_USER_ACTION_EDIT_MODE);
//        $this->switchEditable('article', $edit);

        //TODO: nejdřív vypnu editable a pak teprve volám isPresentedItemActive() - pokud menuItem není active, tak se s vypnutým editable už v metodě isPresentedItemActive() nenačte - ?? obráceně?
        $this->statusPresentationRepo->get()->getUserActions()->setEditableContent($edit);
        $this->addFlashMessage("set editable article $edit");
        if ($edit OR $this->isPresentedItemActive()) {
            return $this->redirectSeeLastGet($request); // 303 See Other
        } else {
            return $this->createResponseRedirectSeeOther($request, ''); // 303 See Other -> home - jinak zůstane prezentovaný poslední segment layoutu, který nyl editován v režimu edit layout
        }
    }

    public function setEditContent(ServerRequestInterface $request) {
        $edit = (new RequestParams())->getParsedBodyParam($request, self::FORM_USER_ACTION_EDIT_CONTENT);
//        $this->switchEditable('article', $edit);

        //TODO: nejdřív vypnu editable a pak teprve volám isPresentedItemActive() - pokud menuItem není active, tak se s vypnutým editable už v metodě isPresentedItemActive() nenačte - ?? obráceně?
        $this->statusPresentationRepo->get()->getUserActions()->setEditableContent($edit);
        $this->addFlashMessage("set editable article $edit");
        if ($edit OR $this->isPresentedItemActive()) {
            return $this->redirectSeeLastGet($request); // 303 See Other
        } else {
            return $this->createResponseRedirectSeeOther($request, ''); // 303 See Other -> home - jinak zůstane prezentovaný poslední segment layoutu, který nyl editován v režimu edit layout
        }
    }

    public function setEditMenu(ServerRequestInterface $request) {
        $edit = (new RequestParams())->getParsedBodyParam($request, self::FORM_USER_ACTION_EDIT_MENU);
//        $this->switchEditable('menu', $edit);
        $this->addFlashMessage("set editable menu $edit");
        $this->statusPresentationRepo->get()->getUserActions()->setEditableMenu($edit);
        if ($edit OR $this->isPresentedItemActive()) {
            return $this->redirectSeeLastGet($request); // 303 See Other
        } else {
            return $this->createResponseRedirectSeeOther($request, ''); // 303 See Other -> home - jinak zůstane prezentovaný poslední articele, který nyl editován v režimu edit article
        }
    }

    private function isPresentedItemActive() {
        $statusPresentation = $this->statusPresentationRepo->get();
        $menuItem = $statusPresentation->getMenuItem();
        return $menuItem ? $menuItem->getActive() : false;
    }
}
