<?php

namespace Component\View\Nav;

use Pes\View\Template\NodeTemplate;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NavSubtreeComponent
 *
 * @author pes2704
 */
class NavSubtreeComponent extends NavCompositeComponentAbstract implements NavSubtreeComponentInterface {

    protected $withTitle;

    protected $componentName;

    protected $itemRendererName;

    /**
     *
     * @param string $componentName
     * @return \Component\Controller\Authored\MenuComponentInterface
     */
    public function setMenuRootName($componentName): NavSubtreeComponentInterface {
        $this->componentName = $componentName;
        return $this;
    }

    /**
     *
     * @param bool $withTitle
     * @return NavSubtreeComponent
     */
    public function withTitleItem($withTitle=false): NavSubtreeComponentInterface {
        $this->withTitle = $withTitle;
        return $this;
    }

    public function getString($data=null) {
        $this->nodeFactory->
        $this->setTemplate(new NodeTemplate($this->nodeFactory->createTag()));

        return parent::getString($data);
    }
}
