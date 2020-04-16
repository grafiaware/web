<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Generated;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Generated\ItemTypeSelectViewModel;

use Pes\Text\Message;

use Pes\Text\Html;

/**
 * Description of ItemTypeRenderer
 *
 * @author pes2704
 */
class ItemTypeRenderer extends HtmlRendererAbstract {

    public function render($data=NULL) {
        return $this->renderPrivate($data);
    }

    private function renderPrivate(ItemTypeSelectViewModel $viewModel) {
        $radioHtml = '';
        foreach ($viewModel->getTypes() as $type) {
            $radioHtml .=Html::tag('label', [],
                                Html::tag('input', ['type'=>"radio", 'name'=>"type", 'value'=>$type->getType()])
                                .$type->getType()
                            );
        }
        return Html::tag('form', [],
                    Html::tag('p', [], Message::t("Vyberte typ obsahu:"))
                    .Html::tag('div', [],
                            $radioHtml
                        )
                    .Html::tag("div", [],
                    Html::tag("button", [], Message::t("Odeslat"))
                    )
                );
    }
//    <form>
//  <p>Please select your preferred contact method:</p>
//  <div>
//    <input type="radio" id="contactChoice1"
//     name="contact" value="email">
//    <label for="contactChoice1">Email</label>
//
//    <input type="radio" id="contactChoice2"
//     name="contact" value="phone">
//    <label for="contactChoice2">Phone</label>
//
//    <input type="radio" id="contactChoice3"
//     name="contact" value="mail">
//    <label for="contactChoice3">Mail</label>
//  </div>
//  <div>
//    <button type="submit">Submit</button>
//  </div>
//</form>


}
