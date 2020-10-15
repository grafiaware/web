<?php
namespace Component\ViewModel\Authored\Paper;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Model\Repository\StatusSecurityRepo;
use Model\Repository\StatusPresentationRepo;
use Model\Repository\StatusFlashRepo;

use Model\Repository\ComponentAggregateRepo;

use Model\Repository\PaperAggregateRepo;
use Model\Entity\ComponentAggregateInterface;
use Model\Entity\PaperAggregateInterface;
use Model\Entity\MenuItemInterface;


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

    /**
     * @var ComponentAggregateInterface
     */
    private $componentAggregate;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo,
            PaperAggregateRepo $paperAggregateRepo,
            ComponentAggregateRepo $componentAggregateRepo
    ) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo, $statusFlashRepo, $paperAggregateRepo);
        $this->componentAggregateRepo = $componentAggregateRepo;
    }

    public function getComponentName() {
        return $this->componentName;
    }

    /**
     * Nastaví jméno komponenty. Jménem komponety se řídí metody getComponent() a getPaper
     * @param string $componentName Jméno komponenty
     */
    public function setComponentName($componentName) {
        $this->componentName = $componentName;
    }

    /**
     * Vrací entitu ComponentAggregate se zadaným jménem a jazykem prezentace. Pokud je položka menu item použitá v komponentě neaktivní nebo neaktuální
     * nebo neexistujw component v db se jménem $this->componentName, vrací null.
     *
     * @return ComponentAggregateInterface|null
     * @throws \LogicException Nebylo nstaveno jméno komponenty
     */
    public function getComponentAggregate(): ?ComponentAggregateInterface {
        if (!isset($this->componentAggregate)) {
            if (!isset($this->componentName)) {
                throw new \LogicException("Není zadáno jméno komponenty. Nelze načíst odpovídající položku menu.");
            }
            $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
            $this->componentAggregate = $this->componentAggregateRepo->getAggregate($langCode, $this->componentName);
        }
        return $this->componentAggregate ?? null;
    }

    public function getMenuItem(): ?MenuItemInterface {
        $componentAggregate = $this->getComponentAggregate();  // může být null - neaktivní nebo neaktuální item v komponentě nebo neexistující component v db se jménem $this->componentName
        if ($componentAggregate) {
            $menuItem = $componentAggregate->getMenuItem();
        }
        return $menuItem ?? null;
    }

}
