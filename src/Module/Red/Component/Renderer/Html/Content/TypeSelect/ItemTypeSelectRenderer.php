<?php
namespace Red\Component\Renderer\Html\Content\TypeSelect;

use Component\Renderer\Html\HtmlRendererAbstract;
use Red\Middleware\Redactor\Controler\ItemEditControler;

use Pes\Text\Message;
use Pes\Text\Html;

/**
 * Description of ItemTypeSelectRendere
 *
 * @author pes2704
 */
class ItemTypeSelectRenderer extends HtmlRendererAbstract {

public function render(iterable $viewModel = NULL) {
        $menuItemUidFk = $viewModel->getMenuItem()->getUidFk();
        $contentGeneratorsTypes = $viewModel->getTypeGenerators();
        $radioHtml = '';
        if (isset($contentGeneratorsTypes)) {
//            foreach ($transitions as $type ) {
                $radioHtml = Html::radio(ItemEditControler::TYPE_VARIABLE_NAME, $contentGeneratorsTypes, [], ['required'=>'1']);
//                $radioHtml .=Html::tag('label', [],
//                                    Html::tag('input', ['type'=>"radio", 'name'=>"type", 'value'=>$type, 'required'=>'1'])
//                                    .$type
//                                );
            }
            return Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/menu/$menuItemUidFk/type"],
                        Html::tag('p', [], Message::t("Vyberte typ obsahu:"))
                        .Html::tag('div', [],
                            $radioHtml
                         )
//                        .Html::tag('label', [], "static path:" . Html::tag('input', ['type'=>"text", 'name'=>"folded", 'value'=>""]))
                        .Html::tag("div", [],
                            Html::tag("button", [], Message::t("Odeslat"))
                        )
                    );
//        }
    }

}
