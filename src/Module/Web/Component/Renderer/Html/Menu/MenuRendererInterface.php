<?php
namespace  Web\Component\Renderer\Html\Menu;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Red\Model\Entity\MenuItemInterface;

/**
 *
 * @author pes2704
 */
interface MenuRendererInterface {
    public function renderItem(MenuItemInterface $item, $isOnPath, $isLeaf, $nextChildrenWrapperHtml='');
    public function renderLevelWrap($levelItemsHtml);
    public function renderMenuWrap($levelsWrapsHtml);

}
