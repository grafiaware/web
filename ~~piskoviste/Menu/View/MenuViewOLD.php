<?php

namespace Menu\Middleware\Menu\View;

use Pes\View\View;
use Pes\View\ViewFactory;
use Pes\View\ViewInterface;

use Pes\View\Renderer\InterpolateRenderer;
use Pes\View\Template\InterpolateTemplate;


use Menu\Menu\Model\MenuListStyles;

use Menu\Menu\View\ListView;
use Menu\Menu\View\Layout\MenuTemplate;

use Pes\Dom\Node\Tag;
use Pes\Dom\Node\Text;

/**
 * Description of menuView
 *
 * @author pes2704
 */
class MenuView extends View {

    public function render($template=NULL, $data=[]) {
        $elementId = 'menu';
        $data['elementId'] = $elementId;

        // 3
        $list = (new ListView())->render(NULL, $data);
        $result[] = "<nav><form action='index.php'>$list</form></nav>";
        // zakomentuj řádek pro vypnutí jQuery
        $result[] = "<script>".(new View())
                ->setTemplate(new InterpolateTemplate('Menu/js/BindMenuToJquery.js'))
                ->setData(['elementId' => $elementId])
                ->render()."</script>";
        $result[] = "<script>".(new View())
                ->setTemplate(new InterpolateTemplate('Menu/js/EditItemName.js'))
                ->render()."</script>";

        return implode(\PHP_EOL, $result);

    }
}
