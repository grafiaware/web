<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored;

use Component\ViewModel\MenuItemViewModel;
use Red\Model\Entity\ItemActionInterface;
use Red\Model\Enum\AuthoredTypeEnum;
use UnexpectedValueException;

/**
 * Description of AuthoredViewModelAbstract
 *
 * @author pes2704
 */
abstract class AuthoredViewModelAbstract extends MenuItemViewModel implements AuthoredViewModelInterface {

    protected $menuItemType;

    abstract public function getAuthoredContentType();

    abstract public function getAuthoredTemplateName();

    abstract public function getAuthoredContentId();


    // zatím mimo interface!
    //
    // item action

    /**
     *
     * @return ItemActionInterface|null
     */
    public function getAuthoredContentAction(): ?ItemActionInterface {
        return $this->status->getItemsAction($this->getAuthoredContentType(), $this->getAuthoredContentId());
    }

    public function userPerformAuthoredContentAction(): bool {
        $itemAction = $this->getAuthoredContentAction();
        if (isset($itemAction)) {
            $loginName = $this->status->getUserLoginName();
            $userPerformActionWithItem = isset($loginName) AND $itemAction->getEditorLoginName()==$loginName;
        } else {
            $userPerformActionWithItem = false;
        }
        return $userPerformActionWithItem;
    }
//    public function userPerformActionWithItem(): bool {
//        $itemAction = $this->getItemAction();
//        $loginAgg = $this->statusSecurityRepo->get()->getLoginAggregate();
//        return isset($itemAction) AND isset($loginAgg) AND $itemAction->getEditorLoginName()==$loginAgg->getLoginName();
//    }

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
                $templateContentPostVar = AuthoredControlerAbstract::ARTICLE_TEMPLATE_CONTENT.$authoredContentId;
                break;
            case AuthoredTypeEnum::PAPER:
                $templateContentPostVar = AuthoredControlerAbstract::PAPER_TEMPLATE_CONTENT.$authoredContentId;
                break;
            case AuthoredTypeEnum::MULTIPAGE:
                $templateContentPostVar = AuthoredControlerAbstract::MULTIPAGE_TEMPLATE_CONTENT.$authoredContentId;
                break;
            default:
                throw new UnexpectedValueException("Neznámý typ item '$type'. Použijte příkaz 'Zpět' a nepoužívejte tento typ obsahu.");
        }
        return $templateContentPostVar;
    }
}
