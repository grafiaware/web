<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\ViewModel\Content\StaticItem;

use Red\Component\ViewModel\Content\MenuItemViewModel;

use Component\ViewModel\StatusViewModelInterface;
use Red\Model\Repository\StaticItemRepoInterface;

use Red\Model\Repository\MenuItemRepoInterface;
use Red\Model\Entity\MenuItemInterface;
use Red\Model\Entity\StaticItemInterface;

use UnexpectedValueException;

/**
 * Description of LanguageSelect
 *
 * @author pes2704
 */
class StaticItemViewModel extends MenuItemViewModel implements StaticItemViewModelInterface {

    /**
     * @var StaticItemRepoInterface
     */
    private $staticRepo;
    
    /**
     * @var StaticItemInterface
     */
    private $staticEntity;

    public function __construct(
            StatusViewModelInterface $status, 
            MenuItemRepoInterface $menuItemRepo,
            StaticItemRepoInterface $staticRepo
            ) {
        parent::__construct($status, $menuItemRepo);
        $this->staticRepo = $staticRepo;
    }
    
    /**
     * 
     * @return string|null
     */
    public function getStaticTemplatePath(): ?string {
        if (!isset($this->staticEntity)) {
            if (isset($this->menuItemId)) {
                $this->staticEntity = $this->staticRepo->getByMenuItemId($this->menuItemId);
                if (!isset($this->staticEntity)) {
                    throw new UnexpectedValueException("Nenačtena static položka z databáze pro menu item id: '{$this->menuItemId}'");
                }
            }
        }

        return $this->staticEntity->getPath() ? $this->staticEntity->getPath().'\\'.$this->staticEntity->getTemplate() : $this->staticEntity->getTemplate();
    }

    public function getIterator(): \Traversable {
        $this->appendData(
                [
                    'menuItem' => $this->getMenuItem(),
                    'staticTemplatePath' => $this->getStaticTemplatePath(),
                ]
                );
        return parent::getIterator();
    }
}
