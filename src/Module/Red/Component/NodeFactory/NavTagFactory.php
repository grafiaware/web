<?php

namespace Red\Component\NodeFactory;

use Pes\Dom\Node\Tag;
use Pes\Dom\Node\Text;

use Pes\Dom\Node\NodeInterface;

use Pes\View\View;
use Pes\View\Template\FileInterpolateTemplate;

use Psr\Container\ContainerInterface;

use Red\Component\ViewModel\Nav\NavViewModelInterface;

//use Menu\Model\HierarchyHooks\MenuListStyles;
use Red\Component\Renderer\Html\ClassMap\ClassMapInterface;

use Pes\View\Renderer\RendererInterface;
use Pes\View\Renderer\StringRenderer;

/**
 * Generuje <nav> tag obsahující strukturu <ul><li> tagů obsahující strom položek menu a tagy <script> s obslužnými javascripty
 *
 * @author pes2704
 */
class NavTagFactory implements NavTagFactoryInterface {

    private $viewModel;
    private $classMap;
    private $ulElementId;

//    private $ulElementId;

    /**
     * @var ContainerInterface
     */
    private $rendererContainer;

    private $itemRendererName;
    private $itemView;
//    private $basePath;
//
//    private $editable;

//    public function __construct( $flattenedTree, ClassMapInterface $classMap, $ulElementId, $basePath, $editable=FALSE) {
//    public function __construct(ClassMapInterface $classMap) {
//        $this->classMap = $classMap;
//        $this->ulElementId = $ulElementId;
//        $this->basePath = $basePath;
//        $this->editable = $editable;
    public function __construct(NavViewModelInterface $viewModel) {
        $this->viewModel = $viewModel;
//        $this->ulElementId = $ulElementId;
    }

    public function setRendererContainer(ContainerInterface $rendererContainer): NavTagFactoryInterface {
        $this->rendererContainer = $rendererContainer;
        return $this;
    }

    public function setItemRendererName($itemRendererName): NavTagFactoryInterface {
        $this->itemRendererName = $itemRendererName;
        return $this;
    }

    public function setClassmap(ClassMapInterface $classMap): NavTagFactoryInterface {
        $this->classMap = $classMap;
        return $this;
    }

    /**
     * K tagu Nav přidá tagy Script pro editaci item a bind k jQuery
     * @param type $rootDepth
     * @return type
     */
    public function createTag(): NodeInterface {
        $this->itemView = (new View())->setRenderer($this->rendererContainer->get($this->itemRendererName));

        $flattenedTree = $this->viewModel->getNodeModels($rootUid, $maxDepth);
        $tagUlWithMenu = $this->createUlTag($flattenedTree);

        // skript pro editaci názvu položky (editable)
        $scriptEditItem = (new Tag\Script())->addChild(new Text\TextView(
                        (new View())->setTemplate(new FileInterpolateTemplate('Middleware/Menu/View/Templates/js/EditItemName.js'))
                    ));

        // skript pro navázání menu na jQuery menu - pomocí id elementu menu (vnější <ul>)
//        $scriptBindToJquery = (new Tag\Script())->addChild(new Text\TextView(
//                        (new View())
//                            ->setTemplate(new FileInterpolateTemplate('Middleware/Menu/View/Templates/js/BindMenuToJquery.js'))
//                            ->setData(['menuUlElementId' => $this->ulElementId])
//                    ));

        $tagNav = (new Tag\Nav(['class'=> 'menu_nav']))
                    ->addChild((new Tag\Form(['action'=>'index.php']))
                        ->addChild(new Tag\Div(['id'=>'debug']))        // pro funkci log() - EditItemName.js
                            ->addChild($tagUlWithMenu))
                ;
//        $tagNav->addChild($scriptEditItem);
        $tagNav->addChild($scriptBindToJquery);

        return $tagNav;
    }

    /**
     * Genuruje tag Ul s vnořenou strukturou li, ul tagů obsahující menu
     *
     * @return Tag\TagInterface
     */
    private function createUlTag($flattenedTree) {

        $rootTag = $this->getMenuWrapNode();
//        $rootTag->getAttributesNode()->setAttribute('id', $this->ulElementId);   // přidám atribut id s hodnotou, na kterou je pověšeno jQuery

        foreach( $flattenedTree as $itemViewModel ) {
            /** @var ItemViewModelInterface $itemViewModel */
            $itemDepth = $itemViewModel->getHierarchyAggregate()->getDepth();
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
        return new Tag\Ul(
//                ['class'=>$this->classMap->get('MenuWrap', 'ul')]
                []
                );
    }

    public function getLevelWrapNode() {
        return new Tag\Ul(
//                ['class'=>$this->classMap->get('LevelWrap', 'ul')]
                []
                );
    }

    public function getItemNode(MenuItemInterface $item) {
        $returnTag =new Tag\Li(
//                ['class'=>$this->classMap->get('Item', 'li')
                  [
                'data-depth'=>$item['depth']]);
        // node text
        $clonedView = clone $this->itemView;
        return $returnTag->addChild(
                new Text\Text(
                        $clonedView->setData(
                            ['item'=>$item,
//                                'basePath'=>$this->basePath   // asi není třeba
                                ]
                        )
                    )
            );
    }

    public function getItemNodeNew(ItemViewModelInterface $itemViewModel) {
        $returnTag = new Tag\Li(
                [
                    'class'=>[
                        $this->classMap->resolve($itemViewModel->isOnPath(), 'Item', 'li.onpath', 'li'),
                        $this->classMap->resolve($itemViewModel->isLeaf(), 'Item', 'li.leaf', 'li'),
                        $this->classMap->resolve($itemViewModel->isPresented(), 'Item', 'li.presented', 'li'),
                        ],
                    'data-depth'=>$itemViewModel->getHierarchyAggregate()->getDepth()
                ]);

        $returnTag->addChild(new Tag\I(
                ['class'=> $this->classMap->get('Item', 'li i1')]
                ));
        $returnTag->addChild((new Tag\A(
                    [
                        'class'=>$this->classMap->get('Item', 'li a'),
                        'href'=>"index.php?list={$itemViewModel->getHierarchyAggregate()->getUid()}"
                    ]
                    ))->addChild(new Text\Text($itemViewModel->getHierarchyAggregate()->getHierarchy()->getTitle()))
                );

        // nemám innerHtml
        $returnTag->addChild(new Tag\I(
                ['class'=>$this->classMap->resolve( ! $itemViewModel->isLeaf(), 'Item', 'li i')]  // negace - není leaf, má dropdown icon
                ));
        return $returnTag;
    }
}
