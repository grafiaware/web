<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored\Menu\Item;

use Model\Entity\MenuNodeInterface;

/**
 * Description of ItemViwModel
 *
 * @author pes2704
 */
class ItemViewModel implements ItemViewModelInterface {

    /**
     * @var MenuNodeInterface
     */
    private $menuNode;

    private $isOnPath;
    private $isLeaf;
    private $isPresented;
    private $readonly;
    private $innerHtml;

    public function __construct(MenuNodeInterface $menuNode, $isOnPath, $isPresented, $readonly, $innerHtml='') {
        $this->menuNode = $menuNode;
        $this->isOnPath = $isOnPath;
        $this->isPresented = $isPresented;
        $this->readonly = $readonly;
        $this->innerHtml = $innerHtml;

        $this->isLeaf = ($this->menuNode->getRightNode() - $this->menuNode->getLeftNode()) == 1;
    }

    /**
     *
     * @return MenuNodeInterface
     */
    public function getMenuNode() {
        return $this->menuNode;
    }

    public function isOnPath() {
        return $this->isOnPath;
    }

    public function isLeaf() {
        return $this->isLeaf;
    }

    public function getIsPresented() {
        return $this->isPresented;
    }
    public function getReadonly() {
        return $this->readonly;
    }

    public function getInnerHtml() {
        return $this->innerHtml;
    }
}
