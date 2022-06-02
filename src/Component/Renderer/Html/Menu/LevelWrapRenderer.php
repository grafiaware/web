<?php
namespace  Component\Renderer\Html\Menu;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\View\Menu\ItemComponentInterface;

use Pes\Type\ContextDataInterface;
use Pes\Text\Html;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Svisle
 *
 * @author pes2704
 */
class LevelWrapRenderer extends HtmlRendererAbstract {

    public function render(iterable $contextData=NULL) {
        // LevelComponent nedostává zádný view model - view předá fo rendereru jen ContextData, který obsahuje vyrenderované komponentní view (tedy html)
            $levelItemsHtml = "";
            /** @var ContextDataInterface $contextData */
            foreach ($contextData as $itemComponentHtml) {
                /** @var ItemComponentInterface $itemComponentHtml */
                $levelItemsHtml .= $itemComponentHtml;
            }

        $ul = Html::tag('ul', ['class'=>[$this->classMap->get('LevelWrap', 'ul')]],$levelItemsHtml);
        return $ul;
    }

}
