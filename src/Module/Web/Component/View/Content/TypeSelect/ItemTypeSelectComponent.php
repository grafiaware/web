<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Web\Component\View\Content\TypeSelect;

use Web\Component\View\ComponentCompositeAbstract;
use Web\Component\ViewModel\Authored\TypeSelect\ItemTypeSelectViewModel;
use Web\Component\Renderer\Html\Content\TypeSelect\ItemTypeSelectRenderer;
use Web\Component\Renderer\Html\Generated\EmptyItemRenderer;

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
