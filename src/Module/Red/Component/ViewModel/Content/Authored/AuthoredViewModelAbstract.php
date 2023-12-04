<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\ViewModel\Content\Authored;

use Red\Component\ViewModel\Content\MenuItemViewModel;

use Red\Middleware\Redactor\Controler\AuthoredControlerAbstract;

use Component\ViewModel\StatusViewModelInterface;
use Red\Model\Repository\MenuItemRepoInterface;
use Red\Model\Repository\ItemActionRepoInterface;

use Red\Model\Entity\ItemActionInterface;
use Red\Model\Enum\AuthoredTypeEnum;
use UnexpectedValueException;

/**
 * Description of AuthoredViewModelAbstract
 *
 * @author pes2704
 */
abstract class AuthoredViewModelAbstract extends MenuItemViewModel implements AuthoredViewModelInterface {

    protected $itemActionRepo;

    protected $menuItemType;

    abstract public function getAuthoredContentType(): string;

    abstract public function getAuthoredTemplateName(): ?string;

    abstract public function getAuthoredContentId(): string;

    public function __construct(
            StatusViewModelInterface $status,
            MenuItemRepoInterface $menuItemRepo,
            ItemActionRepoInterface $itemActionRepo
            ) {
        parent::__construct($status, $menuItemRepo);
        $this->itemActionRepo = $itemActionRepo;
    }
    
    // - z itemActionRepo - podle $menuItem->getId() - dostanu jestli a kdo edituje item
    // - z $this->status->getUserActions()->getUserItemAction($menuItem->getId());  - dostanu jestli přihlášený úživatel zahájil editaci
    //   tohoto itemu v této session
    
    /**
     *
     * @return ItemActionInterface|null
     */
    public function getItemAction(): ?ItemActionInterface {
        // item action hledá pouze podle itemId
        return $this->itemActionRepo->getByItemId($this->getMenuItemId());   // vyhazuje výjimku pokud nebylo zadáno item id
    }

    public function userPerformItemAction(): bool {
//        ve statusu je aktuální itemAction: $this->status->getUserActions()->getUserItemAction($menuItem->getId());  
        $itemAction = $this->getItemAction();
        if (isset($itemAction)) {
            $loginName = $this->statusViewModel->getUserLoginName();
            $editorName = $itemAction->getEditorLoginName();
            $userPerformActionWithItem = $loginName==$editorName;
        } else {
            $userPerformActionWithItem = false;
        }
        return $userPerformActionWithItem;
    }

    /**
     * Vrací jméno, které musí být v rendereru použito jako id pro element, na kterém visí tiny editor.
     * POZOR - id musí být unikátní - jinak selhává tiny selektor - a "nic není vidět"
     *
     * @return string
     * @throws UnexpectedValueException
     */
    public function getTemplateContentPostVarName() {
        $type = $this->getAuthoredContentType();
        // $templateContentPostVar použito jako id pro element, na které visí tiny - POZOR - id musí být unikátní - jinak selhává tiny selektor - a "nic není vidět"
        switch ($type) {
            case AuthoredTypeEnum::ARTICLE:
                $templateContentPostVar = AuthoredControlerAbstract::ARTICLE_TEMPLATE_CONTENT.$this->getAuthoredContentId();
                break;
            case AuthoredTypeEnum::PAPER:
                $templateContentPostVar = AuthoredControlerAbstract::PAPER_TEMPLATE_CONTENT.$this->getAuthoredContentId();
                break;
            case AuthoredTypeEnum::MULTIPAGE:
                $templateContentPostVar = AuthoredControlerAbstract::MULTIPAGE_TEMPLATE_CONTENT.$this->getAuthoredContentId();
                break;
            default:
                throw new UnexpectedValueException("Neznámý typ item '$type'. Použijte příkaz 'Zpět' a nepoužívejte tento typ obsahu.");
        }
        return $templateContentPostVar;
    }
}
