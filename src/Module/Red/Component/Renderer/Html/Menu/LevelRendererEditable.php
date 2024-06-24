<?php
namespace  Red\Component\Renderer\Html\Menu;

use Component\Renderer\Html\HtmlRendererAbstract;
use Red\Component\View\Menu\ItemComponentInterface;

use Red\Component\ViewModel\Menu\LevelViewModelInterface;
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
class LevelRendererEditable extends HtmlRendererAbstract {

    public function render(iterable $contextData=NULL) {
        // LevelComponent nedostává žádný view model - view předá do rendereru jen ContextData, který obsahuje vyrenderované komponentní view (tedy html)
            $levelItemsHtml = "";
            /** @var LevelViewModelInterface $contextData */
            foreach ($contextData as $itemComponentHtml) {
                /** @var ItemComponentInterface $itemComponentHtml */
                $levelItemsHtml .= $itemComponentHtml;
            }
            $ul = Html::tag('ul', ['class'=>[$this->classMap->resolve($contextData->isLastLevel(), 'Level', 'ul.lastLevel', 'ul')]],$levelItemsHtml);
        return $ul;
    }

}
