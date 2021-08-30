<?php

namespace Menu\Middleware\Menu\View;

use Pes\View\View;
use Menu\Menu\Model\MenuListStyles;

/**
 * Description of ListView
 *
 * @author pes2704
 */
class ListView extends View {
    
    const ROOT_DEPTH = 0;    
    
    /**
     *
     * Render a nested set into a HTML list
     *
     * @param       array   $flatenedTree
     * @return      string  the formated tree
     *
     */
    public function render($template=NULL, $data=[]) {
        $flatenedTree = $data['flatenedTree'];
        $styles = $data['styles'];
        $elementId = $data['elementId'];
        
        
        $itemView = new ItemView();
        $currDepth = self::ROOT_DEPTH-1;  
        $firstItem = TRUE;
        foreach( $flatenedTree as $item ) {
            $itemDepth = $item['depth'];
                if($itemDepth > $currDepth) {
                    if ($firstItem) {
                        $result[] = "<ul id=\"$elementId\" class=\"{$styles->getStyle($itemDepth, 'ul')}\" data-depth=\"{$itemDepth}\">";
                        $firstItem = FALSE;
                    } else {
                        $result[] = "<ul class=\"{$styles->getStyle($itemDepth, 'ul')}\" data-depth=\"{$itemDepth}\">";
                    }
                    $currDepth = $itemDepth;
                } elseif ($itemDepth < $currDepth) {
                    for ($i = 1; $i <= $currDepth-$itemDepth; $i++) {
                        $result[] = "</li>";
                        $result[] = '</ul>'; 
                    }
                    $currDepth = $itemDepth;
                }

            $result[] = "<li class=\"{$styles->getStyle($itemDepth, 'li')}\" data-depth=\"{$itemDepth}\">";
            $result[] = $itemView->render(NULL, $item);
            
        }        
        for ($i = 1; $i <= $currDepth; $i++) {
            $result[] = "</li>";
            $result[] = '</ul>'; 
        }
        return implode(PHP_EOL, $result);   
    }
}
