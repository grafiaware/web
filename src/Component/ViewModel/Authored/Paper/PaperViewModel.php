<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored\Paper;

use Model\Entity\PaperAggregateInterface;
use Model\Entity\PaperAggregate;

use Component\ViewModel\Authored\AuthoredViewModelAbstract;

use Model\Repository\StatusSecurityRepo;
use Model\Repository\StatusPresentationRepo;
use Model\Repository\StatusFlashRepo;
use Model\Repository\PaperAggregateRepo;

/**
 * Description of PaperViewModelAnstract
 *
 * @author pes2704
 */
class PaperViewModel extends AuthoredViewModelAbstract implements PaperViewModelInterface, \IteratorAggregate {
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

    public function setItemId($menuItemId) {
        $this->menuItemId = $menuItemId;
    }

    public function getInfo(): string {
        return "paperByReference";
    }

    /**
     * Vrací PaperAggregate příslušný k menuItem nebo null.
     * MenuItem musí být aktivní nebo prezentace musí být v reřimu article editable - jinak repository nevrací menuItem a nevznikne PaperAggregate, metoda vrací null.
     *
     * Pokud PaperAggregate dosud neexistuje (není persitován, není vrácen z repository v režimu article editable) vytvoří nový objekt PaperAggregate.
     *
     * @return PaperAggregateInterface|null
     */
    public function getPaperAggregate(): ?PaperAggregateInterface {
            $paperAggregate = $this->paperAggregateRepo->getByReference($this->menuItemId);
            if (!isset($paperAggregate) AND $this->isArticleEditable()) {
                $paperAggregate = new PaperAggregate();
                $paperAggregate->setEditor($this->statusSecurityRepo->get()->getUser()->getUserName());
            }
        return $paperAggregate ?? null;
    }

    public function getIterator() {
        return new \ArrayObject(
                [
                    'paperAggregate' => $this->getPaperAggregate()
                ]
            );
    }
}