<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored\Paper;

use Red\Model\Entity\PaperAggregatePaperContentInterface;
use Red\Model\Entity\PaperInterface;

use Component\ViewModel\Authored\AuthoredViewModelAbstract;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;
use Red\Model\Repository\ItemActionRepo;
use Red\Model\Repository\MenuItemRepoInterface;

use Red\Model\Repository\PaperAggregateContentsRepo;

use TemplateService\TemplateSeekerInterface;
use Red\Model\Enum\AuthoredEnum;
/**
 * Description of PaperViewModelAnstract
 *
 * @author pes2704
 */
class PaperViewModel extends AuthoredViewModelAbstract implements PaperViewModelInterface {

    /**
     * @var PaperAggregateContentsRepo
     */
    protected $paperAggregateRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo,
            ItemActionRepo $itemActionRepo,
            MenuItemRepoInterface $menuItemRepo,
            TemplateSeekerInterface $templateSeeker,
            PaperAggregateContentsRepo $paperAggregateRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo, $statusFlashRepo, $itemActionRepo, $menuItemRepo, $templateSeeker);
        $this->paperAggregateRepo = $paperAggregateRepo;
    }

    /**
     * Vrací typ položky. Používá AuthoredEnum.
     * Obvykle je metoda volána z metody Front kontroleru.
     *
     * @param type $menuItemType
     */
    public function getItemType() {
        return AuthoredEnum::PAPER;
    }
    
    public function getItemTemplate() {
        return $this->getPaper()->getTemplate();
    }

    /**
     * {@inheritdoc}
     *
     * MenuItem musí být aktivní nebo prezentace musí být v režimu article editable - jinak repository nevrací menuItem a nevznikne PaperAggregate, metoda vrací null.
     *
     * @return PaperAggregatePaperContentInterface|null
     */
    public function getPaper(): ?PaperAggregatePaperContentInterface {
        if (isset($this->menuItemId)) {
            $paper = $this->paperAggregateRepo->getByReference($this->menuItemId);
        }
        return $paper ?? null;
    }

    /**
     * ContextData mají jako default typ iterátoru vraceného IteratorAggregate (v konstruktoru) nastaveno ArrayIterator
     * V této metodě lze:
     * - pro ViewModel, který nemá jako předka ViewModel: volat ->appendData() s parametrem typu pole a skončit, je vrácen ArrayIterator objektu ContextData
     * - pro ViewModel, který má jako předka ViewModel: volat ->appendData() s parametrem typu pole a následně parent::getIterator() - připoji se také položky iterátoru, které přidává rodič
     *
     * Pozor! Všechny položky iterátoru musí být převeditelné na string. V průběhu renderování komponentních view dochází k převádění na string.
     * Data typu objekt, která je potřeba předat do Php šablony lze přidat metodou šablony setSharedData().
     *
     * @return type
     */
    public function getIterator() {
        //TODO: isEditable - asi nevyužito - je v PaperViewModel a ArticleViewModel
        $this->appendData(['isEditable'=> $this->presentEditableContent()]);
        return parent::getIterator();
    }


}