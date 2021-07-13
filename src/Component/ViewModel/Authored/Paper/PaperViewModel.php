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
use Red\Model\Repository\PaperAggregateRepo;

/**
 * Description of PaperViewModelAnstract
 *
 * @author pes2704
 */
class PaperViewModel extends AuthoredViewModelAbstract implements PaperViewModelInterface {

    /**
     * @var PaperAggregateRepo
     */
    protected $paperAggregateRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo,
            PaperAggregateRepo $paperAggregateRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo, $statusFlashRepo);
        $this->paperAggregateRepo = $paperAggregateRepo;
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

    public function getIterator() {
        return new \ArrayObject(
                        ['paperAggregate'=> $this->getPaper(), 'isEditable'=> $this->userCanEdit()]
                );  // nebo offsetSet po jedné hodnotě
    }


}