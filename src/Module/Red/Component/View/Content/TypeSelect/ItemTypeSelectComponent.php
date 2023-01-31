<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\View\Content\TypeSelect;

use Red\Component\View\ComponentCompositeAbstract;
use Red\Component\ViewModel\Authored\TypeSelect\ItemTypeSelectViewModel;
use Red\Component\Renderer\Html\Content\TypeSelect\ItemTypeSelectRenderer;
use Red\Component\Renderer\Html\Generated\EmptyItemRenderer;

/**
 * Description of ItemTypeSelectComponent
 *
 * @author pes2704
 */
class ItemTypeSelectComponent extends ComponentCompositeAbstract {

    /**
     * @var ItemTypeSelectViewModel
     */
    protected $contextData;

    public function getString() {
        return parent::getString();
    }
}
