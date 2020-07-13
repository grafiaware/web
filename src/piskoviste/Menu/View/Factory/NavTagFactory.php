<?php

namespace Menu\Middleware\Menu\View\Factory;

use Pes\Dom\Node\Tag;
use Pes\Dom\Node\Text;

use Pes\View\View;
use Pes\View\Template\FileInterpolateTemplate;

use Menu\Middleware\Menu\View\Factory\UlTagFactory;

//use Menu\Model\HierarchyHooks\MenuListStyles;
use Menu\Component\Renderer\Html\ClassMap\ClassMap;

/**
 * Generuje <nav> tag obsahující strukturu <ul><li> tagů obsahující strom položek menu a tagy <script> s obslužnými javascripty
 *
 * @author pes2704
 */
class NavTagFactory {

    private $ulTagFactory;
    private $ulElementId;

    public function __construct(UlTagFactory $ulTagFactory, $ulElementId) {
        $this->ulTagFactory = $ulTagFactory;
        $this->ulElementId = $ulElementId;
    }
    /**
     * K tagu Nav přidá tagy Script pro editaci item a bind k jQuery
     * @param type $rootDepth
     * @return type
     */
    public function createTag($rootDepth = NULL) {
        $tagUlWithMenu = $this->ulTagFactory->createTag($rootDepth);

        // skript pro editaci názvu položky (editable)
        $scriptEditItem = (new Tag\Script())->addChild(new Text\TextView(
                        (new View())->setTemplate(new FileInterpolateTemplate('Middleware/Menu/View/Templates/js/EditItemName.js'))
                    ));

        // skript pro navázání menu na jQuery menu - pomocí id elementu menu (vnější <ul>)
        $scriptBindToJquery = (new Tag\Script())->addChild(new Text\TextView(
                        (new View())
                            ->setTemplate(new FileInterpolateTemplate('Middleware/Menu/View/Templates/js/BindMenuToJquery.js'))
                            ->setData(['menuUlElementId' => $this->ulElementId])
                    ));

        $tagNav = (new Tag\Nav(['class'=> 'menu_nav']))
                    ->addChild((new Tag\Form(['action'=>'index.php']))
                        ->addChild(new Tag\Div(['id'=>'debug']))        // pro funkci log() - EditItemName.js
                            ->addChild($tagUlWithMenu))
                ;
//        $tagNav->addChild($scriptEditItem);
        $tagNav->addChild($scriptBindToJquery);

        return $tagNav;
    }
}
