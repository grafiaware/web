<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\MenuItem\Authored\Paper;

use Red\Model\Entity\PaperAggregatePaperContentInterface;
use Red\Model\Entity\PaperInterface;

use Component\ViewModel\MenuItem\Authored\AuthoredViewModelAbstract;

use Component\ViewModel\StatusViewModelInterface;
use Red\Model\Repository\MenuItemRepoInterface;

use Red\Model\Repository\PaperAggregateContentsRepo;

use Red\Model\Enum\AuthoredTypeEnum;

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
            StatusViewModelInterface $status,
            MenuItemRepoInterface $menuItemRepo,
            PaperAggregateContentsRepo $paperAggregateRepo
            ) {
        parent::__construct($status, $menuItemRepo);
        $this->paperAggregateRepo = $paperAggregateRepo;
    }

    /**
     * Vrací typ položky. Používá AuthoredEnum.
     * Obvykle je metoda volána z metody Front kontroleru.
     *
     * @param type $menuItemType
     */
    public function getAuthoredContentType(): string {
        return AuthoredTypeEnum::PAPER;
    }

    public function getAuthoredTemplateName(): ?string {
        $paper = $this->getPaper();
        return isset($paper) ? $paper->getTemplate() : null;
    }

    public function getAuthoredContentId(): string {
        return $this->getPaper()->getId();
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

}