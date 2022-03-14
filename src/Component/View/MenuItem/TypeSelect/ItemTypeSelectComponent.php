<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\MenuItem\TypeSelect;

use Component\View\MenuItem\MenuItemComponentAbstract;
use Component\ViewModel\Authored\TypeSelect\ItemTypeSelectViewModel;
use Component\Renderer\Html\MenuItem\TypeSelect\ItemTypeSelectRenderer;
use Component\Renderer\Html\Generated\EmptyItemRenderer;

/**
 * Description of ItemTypeSelectComponent
 *
 * @author pes2704
 */
class ItemTypeSelectComponent extends MenuItemComponentAbstract {

    /**
     * @var ItemTypeSelectViewModel
     */
    protected $contextData;

    public function beforeRenderingHook(): void {
        if($this->statusViewModel->presentEditableContent()) {
            $this->setRendererName(ItemTypeSelectRenderer::class);
        } else {
            $this->setRendererName(EmptyItemRenderer::class);
        }
    }

    public function getString() {
        return parent::getString();
    }
}
