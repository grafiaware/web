<?php

namespace Menu\Middleware\Menu\View;

use Pes\View\View;
use Pes\View\ViewFactory;
use Pes\View\StringableView;
use Pes\View\ViewInterface;

use Pes\View\Renderer\InterpolateRenderer;


use Menu\Menu\Model\MenuListStyles;

use Menu\Menu\View\ListView;

use Pes\Dom\Node\Tag;
use Pes\Dom\Node\Text;

/**
 * Description of menuView
 *
 * @author pes2704
 */
class MenuTagView extends View {
        
    public function render($template=NULL, $data=[]) {
        $elementId = 'menu';
        $data['elementId'] = $elementId;

        $tag = (new MenuLayout())->getIcon($data);
        return ViewFactory::viewWithTag($tag)->render();  
    }
}
