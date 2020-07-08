<?php
namespace Component\ViewModel\Authored\Paper;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Model\Repository\StatusSecurityRepo;
use Model\Repository\StatusPresentationRepo;

use Model\Repository\ComponentAggregateRepo;

use Model\Repository\PaperAggregateRepo;
use Model\Entity\ComponentAggregateInterface;
use Model\Entity\PaperAggregateInterface;


/**
 * Description of NamedPaperViewModel
 *
 * @author pes2704
 */
class NamedPaperViewModel extends PaperViewModelAbstract implements NamedPaperViewModelInterface {

    /**
     * @var ComponentAggregateRepo
     */
    protected $componentAggregateRepo;

    private $componentName;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            PaperAggregateRepo $paperAggregateRepo,
            ComponentAggregateRepo $componentAggregateRepo
    ) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo, $paperAggregateRepo);
        $this->componentAggregateRepo = $componentAggregateRepo;
    }

    /**
     * Nastaví jméno komponenty. Jménem komponety se řídí metody getComponent() a getPaper
     * @param string $componentName Jméno komponenty
     */
    public function setComponentName($componentName) {
        $this->componentName = $componentName;
    }

    /**
     * Vrací entitu ComponentAggregate se zadaným jménem.
     *
     * @return ComponentAggregateInterface|null
     * @throws \LogicException
     */
    public function getComponentAggregate(): ?ComponentAggregateInterface {
        if (!isset($this->componentName)) {
            throw new \LogicException("Není zadáno jméno komponenty. Nelze načíst odpovídající položku menu.");
        }
        $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
        return $this->componentAggregateRepo->getAggregate($langCode, $this->componentName);
    }

    /**
     * Vrací PaperAggregate příslušný ke komponentě se zadaným jménem.
     *
     * @return PaperAggregateInterface|null
     */
    public function getPaperAggregate(): ?PaperAggregateInterface {
        $menuItemId = $this->getComponentAggregate()->getMenuItem()->getId();
        return $this->paperAggregateRepo->getByReference($menuItemId) ?? NULL;   // repo z PaperViewModelAbstract
    }
}
