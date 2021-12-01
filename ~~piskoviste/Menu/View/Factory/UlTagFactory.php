<?php

namespace Menu\Middleware\Menu\View\Factory;

use Menu\Middleware\Menu\View\ItemView;

use Pes\Dom\Node\Tag;
use Pes\Dom\Node\Text;

use Menu\Psr\Container\ContainerInterface;
//use Menu\Model\HierarchyHooks\MenuListStyles;

// Components
use Menu\Component\Renderer\Html\ClassMap\ClassMap;
use Menu\Component\ViewModel\Menu\MenuViewModelInterface;
use Menu\Component\ViewModel\Menu\Item\ItemViewModelInterface;

// Model
use Menu\Model\Entity\MenuItemInterface;


use Pes\View\View;
use Menu\Middleware\Menu\View\Renderer\ItemRenderer;

/**
 * Description of ListView
 *
 * @author pes2704
 */
class UlTagFactory {

    /**
     *
     * @var ItemViewModelInterface array of
     */
    private $flattenedTree;

    private $classMap;
    private $ulElementId;

    private $itemView;
    private $basePath;

    private $editable;

    public function __construct( $flattenedTree, ClassMap $classMap, $ulElementId, $basePath, $editable=FALSE) {
        $this->flattenedTree = $flattenedTree;
        $this->classMap = $classMap;
        $this->ulElementId = $ulElementId;
        $this->basePath = $basePath;
        $this->editable = $editable;

        $this->itemView = (new View())->setRenderer(new ItemRenderer());
    }

    /**
     * Genuruje tag Nav - strukturu ul,li tagů obsahující strukturu menu
     *
     * @return Tag\TagInterface
     */
    public function createTag() {

        $rootTag = $this->getMenuWrapNode();
        $rootTag->getAttributesNode()->setAttribute('id', $this->ulElementId);   // přidám atribut id s hodnotou, na kterou je pověšeno jQuery

        foreach( $this->flattenedTree as $itemViewModel ) {
            $itemDepth = $itemViewModel->getMenuNode()->getDepth();
            if (!isset($currDepth)) {
                $currDepth = $itemDepth-1;
            }
            if ($itemDepth <= $currDepth+1) {
                if($itemDepth > $currDepth) {
                    if (!isset($currentLevelTag)) {
                        $currentLevelTag = $rootTag;
                    } else {
                        $currentLevelTag = $this->getLevelWrapNode();
                        $currenItemTag->addChild($currentLevelTag);
                    }
                } elseif ($itemDepth < $currDepth) {
                    for ($i = 1; $i <= $currDepth-$itemDepth; $i++) {
                        $currentLevelTag = $currentLevelTag->getParent()->getParent();    // pro jednu úroveň stromu vznikne <ul> a v něm <li>
                    }
                }
                $currenItemTag = $this->getItemNodeNew($itemViewModel);
                $currentLevelTag->addChild($currenItemTag);
                $currDepth = $itemDepth;
            } else {
                user_error("Struktura stromu menu není spojitá, větev neobsahuje uzly všech úrovní (hloubky). Uzel s titulkem {$itemViewModel['title']} je v hloubce {$itemViewModel['depth']} a jeho předchůdce je v hloubce $currDepth. Pravděpodobně "
                        . "jsou ve stromu menu nepublikované uzly v úrovni nad uzlem publikovaným a ten tak není přístupný.", E_USER_NOTICE);
            }
        }

        return $rootTag;

    }

    public function getMenuWrapNode() {
        return new Tag\Ul(['class'=>$this->classMap->getClass('MenuWrap', 'ul')]);
    }

    public function getLevelWrapNode() {
        return new Tag\Ul(['class'=>$this->classMap->getClass('LevelWrap', 'ul')]);
    }

    public function getItemNode(MenuItemInterface $item) {
        $returnTag =new Tag\Li(['class'=>$this->classMap->getClass('Item', 'li'), 'data-depth'=>$item['depth']]);
        // hned generuji obsah Li z ItemView - používám node text
        return $returnTag->addChild(
                new Text\Text(
                        $this->itemView->setData(
                            ['item'=>$item, 'basePath'=>$this->basePath]
                        )->getString()
                    )
            );
    }

    public function getItemNodeNew(ItemViewModelInterface $itemViewModel) {
        $returnTag = new Tag\Li(
                [
                    'class'=>[
                        $this->classMap->resolveClass($itemViewModel->isOnPath(), 'Item', 'li.onpath', 'li'),
                        $this->classMap->resolveClass($itemViewModel->isLeaf(), 'Item', 'li.leaf', 'li'),
                        $this->classMap->resolveClass($itemViewModel->isPresented(), 'Item', 'li.presented', 'li'),
                        ],
                    'data-depth'=>$itemViewModel->getMenuNode()->getDepth()
                ]);

        $returnTag->addChild(new Tag\I(
                ['class'=> $this->classMap->getClass('Item', 'li i1')]
                ));
        $returnTag->addChild((new Tag\A(
                    [
                        'class'=>$this->classMap->getClass('Item', 'li a'),
                        'href'=>"index.php?list={$itemViewModel->getMenuNode()->getUid()}"
                    ]
                    ))->addChild(new Text\Text($itemViewModel->getMenuNode()->getHierarchy()->getTitle()))
                );

        // nemám innerHtml
        $returnTag->addChild(new Tag\I(
                ['class'=>$this->classMap->resolveClass( ! $itemViewModel->isLeaf(), 'Item', 'li i')]  // negace - není leaf, má dropdown icon
                ));
        return $returnTag;
    }
}
