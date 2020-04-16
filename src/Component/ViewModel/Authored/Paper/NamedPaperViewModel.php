<?php
namespace Component\ViewModel\Authored\Paper;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Model\Entity\MenuNodeInterface;
use Model\Entity\ComponentInterface;
use Model\Entity\PaperInterface;


/**
 * Description of NamedPaperViewModel
 *
 * @author pes2704
 */
class NamedPaperViewModel extends PaperViewModelAbstract implements NamedPaperViewModelInterface {

    private $componentName;
    private $item;

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
    public function getComponent() {
        if (!isset($this->componentName)) {
            throw new \LogicException("Není zadáno jméno komponenty. Nelze načíst odpovídající položku menu.");
        }
        return $this->componentRepo->get($this->componentName);
    }

    /**
     * Metoda nemá parametr. Vrací položku menu odpovídající komponentě se jménem zadaným metodou setComponentName($componentName).
     * V závislosti na stavu prezentace vrací všechny položky nebo pro stav presentPublishOnly jen aktivní a aktuální položky.
     *
     * @return MenuNodeInterface
     */
    public function getMenuNode() {
        $active = $actual = $this->presentOnlyPublished();
        return $this->menuRepo->get($this->getStatusPresentation()->getLanguage()->getLangCode(), $this->getComponent()->getUidFk(), $active, $actual);
    }

    /**
     * Vrací paper příslušný k položce menu.
     *
     * @param MenuNodeInterface $menuNode
     * @return PaperInterface
     */
    public function getPaper() {
        $menuNode = $this->getMenuNode();
        return isset($menuNode) ? $this->paperRepo->get($menuNode->getMenuItem()->getId()) : NULL;
    }
}
