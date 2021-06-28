<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Generated;

use Component\View\ComponentAbstract;
use Component\ViewModel\Generated\ItemTypeSelectViewModel;
use Component\Renderer\Html\Generated\ItemTypeRenderer;
use Component\Renderer\Html\Generated\EmptyItemRenderer;

/**
 * Description of ItemTypeSelectComponent
 *
 * @author pes2704
 */
class ItemTypeSelectComponent extends ComponentAbstract {

    /**
     * @var ItemTypeSelectViewModel
     */
    protected $contextData;

    public function __construct(ItemTypeSelectViewModel $viewModel) {
        $this->contextData = $viewModel;
    }

    public function beforeRenderingHook(): void {
        if($this->contextData->isArticleEditable()) {
            $this->setRendererName(ItemTypeRenderer::class);
        } else {
            $this->setRendererName(EmptyItemRenderer::class);
        }
    }
}
