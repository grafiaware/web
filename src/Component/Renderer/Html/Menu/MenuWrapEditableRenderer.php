<?php
namespace Component\Renderer\Html\Menu;

use Component\Renderer\Html\HtmlRendererAbstract;
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
class MenuWrapEditableRenderer extends HtmlRendererAbstract {

    public function render($menuLevelHtml=NULL) {
        return
        Html::tag('form', [],
            Html::tag('ul', ['class'=>$this->classMap->getClass('MenuWrap', 'ul')],
                $menuLevelHtml
            )
        );
    }
}
