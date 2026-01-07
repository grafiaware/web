<?php
namespace Red\Component\Renderer\Html\Content\TypeSelect;

use Component\Renderer\Html\HtmlRendererAbstract;
use Red\Middleware\Redactor\Controler\ItemEditControler;
use Red\Component\ViewModel\Content\TypeSelect\ItemTypeSelectViewModelInterface;
use Pes\Text\Message;
use Pes\Text\Html;

/**
 * Description of ItemTypeSelectRendere
 *
 * @author pes2704
 */
class ItemTypeSelectRenderer extends HtmlRendererAbstract {

    public function render(iterable $viewModel = NULL) {
        /** @var ItemTypeSelectViewModelInterface $viewModel */
        $menuItemUidFk = $viewModel->getMenuItem()->getUidFk();
        $contentGeneratorsTypes = $viewModel->getTypeGenerators();
        $radioHtml = '';
        if (isset($contentGeneratorsTypes)) {
            $radioHtml = Html::radio(ItemEditControler::TYPE_VARIABLE_NAME, $contentGeneratorsTypes, [], ['required'=>'1']);
        }
        return Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/menu/$menuItemUidFk/type"],
                    Html::tag('p', [], Message::t("Vyberte typ obsahu:"))
                    .Html::tag('div', ['class'=>'item-type'], //'class'=>$this->classMap->get('Template', 'div.templateItemType')
                        $radioHtml
                     )
                    .Html::tag('p', [])
                    .Html::tag("div", [],
                        Html::tag("button", [], Message::t("Odeslat"))
                    )
                );
    }

}
