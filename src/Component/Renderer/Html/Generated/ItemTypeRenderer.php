<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Generated;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Generated\ItemTypeSelectViewModel;

use Pes\View\Renderer\RendererModelAwareInterface;

use Pes\Text\Message;
use Pes\Text\Html;

/**
 * Description of ItemTypeRenderer
 *
 * @author pes2704
 */
class ItemTypeRenderer extends HtmlRendererAbstract  {

    public function render(iterable $viewModel=NULL) {
        /** @var ItemTypeSelectViewModel $viewModel */
        $menuItemUidFk = $viewModel->getMenuItem()->getUidFk();
        $transitions = $viewModel->getTypeTransitions()[];
        $radioHtml = '';
        if (isset($transitions)) {
            foreach ($transitions as $type ) {
                $radioHtml .=Html::tag('label', [],
                                    Html::tag('input', ['type'=>"radio", 'name'=>"type", 'value'=>$type, 'required'=>'1'])
                                    .$type
                                );
            }
            return Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/menu/$menuItemUidFk/type"],
                        Html::tag('p', [], Message::t("Vyberte typ obsahu:"))
                        .Html::tag('div', [],
                            $radioHtml
                         )
                        .Html::tag('label', [], "static path:" . Html::tag('input', ['type'=>"text", 'name'=>"folded", 'value'=>""]))
                        .Html::tag("div", [],
                            Html::tag("button", [], Message::t("Odeslat"))
                        )
                    );
        }
    }
}
