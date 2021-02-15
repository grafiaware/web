<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored\Paper;

use Model\Entity\PaperAggregateInterface;
use Model\Entity\PaperInterface;

use Component\ViewModel\Authored\AuthoredViewModelAbstract;

use Model\Repository\StatusSecurityRepo;
use Model\Repository\StatusPresentationRepo;
use Model\Repository\StatusFlashRepo;
use Model\Repository\PaperAggregateRepo;
use GeneratorService\Paper\PaperServiceInterface;

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

    /**
     * @var PaperServiceInterface
     */
//    protected $paperService;


    private $menuItemId;

    private $templateVariables = [];

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo,
            PaperAggregateRepo $paperAggregateRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo, $statusFlashRepo);
        $this->paperAggregateRepo = $paperAggregateRepo;
//        $this->paperService = $paperService;
    }

    public function setItemId($menuItemId) {
        $this->menuItemId = $menuItemId;
    }

    /**
     * {@inheritdoc}
     *
     * MenuItem musí být aktivní nebo prezentace musí být v reřimu article editable - jinak repository nevrací menuItem a nevznikne PaperAggregate, metoda vrací null.
     *
     * Pokud PaperAggregate dosud neexistuje (není persitován, není vrácen z repository v režimu article editable) vytvoří nový objekt PaperAggregate.
     *
     * @return PaperAggregateInterface|null
     */
    public function getPaper(): ?PaperInterface {
        if (isset($this->menuItemId)) {
                $paper = $this->paperAggregateRepo->getByReference($this->menuItemId);
//            if (!isset($paper)) {
//                $paper = $this->paperService->initialize($this->menuItemId);
//            }
        }

        return $paper ?? null;
    }

    public function getIterator() {
        return new \ArrayObject(
                array_merge(
                        $this->templateVariables,
                        ['paperAggregate'=> $this->getPaper()]
                ));  // nebo offsetSet po jedné hodnotě
    }
}