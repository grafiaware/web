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
use Model\Entity\ComponentInterface;
use Model\Entity\MenuItemPaperAggregateInterface;


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
     * Vrací entitu komponenty.
     *
     * @return ComponentInterface
     * @throws \LogicException
     */
    public function getComponent(): ComponentInterface {
        if (!isset($this->componentName)) {
            throw new \LogicException("Není zadáno jméno komponenty. Nelze načíst odpovídající položku menu.");
        }
        return $this->componentAggregateRepo->get($this->componentName);
    }

    /**
     * Vrací menuItemAggregate příslušný ke komponentě se zadaným jménem.
     *
     * @return MenuItemPaperAggregateInterface|null
     */
    public function getMenuItemPaperAggregate(): ?MenuItemPaperAggregateInterface {
        $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
        $uid = $this->getComponent()->getUidFk();
//        $active = $actual = $this->presentOnlyPublished();
        $this->paperAggregateRepo->setOnlyPublishedMode(true);
        return $this->menuItemAggregateRepo->get($langCode, $uid) ?? NULL;
    }
}
