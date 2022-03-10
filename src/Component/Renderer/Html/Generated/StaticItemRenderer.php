<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Generated;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Authored\TypeSelect\ItemTypeSelectViewModel;

use Pes\View\Renderer\RendererModelAwareInterface;

use Pes\Text\Message;
use Pes\Text\Html;

/**
 * Description of ItemTypeRenderer
 *
 * @author pes2704
 */
class StaticItemRenderer extends HtmlRendererAbstract implements RendererModelAwareInterface {

    public function render($data=NULL) {
        /** @var ItemTypeSelectViewModel $viewModel */
        $viewModel = $this->viewModel;
        $typeFk = $viewModel->getMenuItem()->getTypeFk();
        $menuItemUidFk = $viewModel->getMenuItem()->getUidFk();
        $radioHtml = '';
        $transitions = $viewModel->getTypeTransitions()[$typeFk];
        if (isset($transitions)) {
            foreach ($transitions as $type ) {
                $radioHtml .=Html::tag('label', [],
                                    Html::tag('input', ['type'=>"radio", 'name'=>"type", 'value'=>$type, 'required'=>'1'])
                                    .$type
                                );
            }
            return Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/static/$menuItemUidFk/type"],
                        Html::tag('p', [], Message::t("Vyberte typ obsahu:"))
                        .Html::tag('div', [],
                            $radioHtml
                         )
                        .Html::tag('label', [], "static path:" . Html::tag('input', ['type'=>"text", 'name'=>"folded", 'value'=>""]))
                        .Html::tag("div", [],
                            Html::tag("button", [], Message::t("Odeslat"))
                        )
                    );
        } else {
            throw new UnexpectedValueException("No transitions for menu item type '$typeFk'.");
        }

    }
}
