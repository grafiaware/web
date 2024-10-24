<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\ViewModel\Content\Authored\Paper;

use Red\Model\Entity\PaperAggregatePaperSectionInterface;
use Red\Model\Entity\PaperInterface;

use Red\Component\ViewModel\Content\Authored\AuthoredViewModelAbstract;

use Component\ViewModel\StatusViewModelInterface;
use Red\Model\Repository\MenuItemRepoInterface;
use Red\Model\Repository\ItemActionRepoInterface;

use Red\Model\Repository\PaperAggregateSectionsRepo;

use Red\Model\Enum\AuthoredTypeEnum;
use Red\Middleware\Redactor\Controler\SectionsControler;

/**
 * Description of PaperViewModelAnstract
 *
 * @author pes2704
 */
class PaperViewModel extends AuthoredViewModelAbstract implements PaperViewModelInterface {

    /**
     * @var PaperAggregateSectionsRepo
     */
    protected $paperAggregateRepo;

    /**
     *
     * @var PaperAggregatePaperSectionInterface
     */
    private $paper;

    public function __construct(
            StatusViewModelInterface $status,
            MenuItemRepoInterface $menuItemRepo,
            ItemActionRepoInterface $itemActionRepo,
            PaperAggregateSectionsRepo $paperAggregateRepo
            ) {
        parent::__construct($status, $menuItemRepo, $itemActionRepo);
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
     * @return PaperAggregatePaperSectionInterface|null
     */
    public function getPaper(): ?PaperAggregatePaperSectionInterface {
        if (!isset($this->paper)) {
            if (isset($this->menuItemId)) {
                $this->paper = $this->paperAggregateRepo->getByMenuItemId($this->menuItemId);
            }
        }
        return $this->paper ?? null;
    }

    
    public function isSectionPasteMode(): bool {
        $cut = $this->statusViewModel->getFlashPostCommand(SectionsControler::POST_COMMAND_CUT);
        $copy = $this->statusViewModel->getFlashPostCommand(SectionsControler::POST_COMMAND_COPY);
        return ($cut OR $copy);
    }
}