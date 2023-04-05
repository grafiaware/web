<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace  Red\Component\Renderer\Html\Menu;

use Pes\View\Renderer\RendererInterface;
use Component\Renderer\Html\HtmlRendererAbstract;
use Red\Component\Renderer\Html\Menu\LevelRenderer;
use Red\Component\Renderer\Html\Menu\ItemRenderer;
use Red\Component\ViewModel\Menu\Item\ItemViewModelInterface;

/**
 * Description of MenuWrapRndererAbstract
 *
 * @author pes2704
 */
abstract class MenuWrapRendererAbstract extends HtmlRendererAbstract implements MenuWrapRendererInterface {

    /**
     * @var LevelWrapRenderer
     */
//    private $levelWrapRenderer;

    /**
     * @var ItemRenderer
     */
//    private $itemRenderer;
//
//    public function setLevelWrapRenderer(RendererInterface $levelWrapRenderer): void {
//        $this->levelWrapRenderer = $levelWrapRenderer;
//    }

//    protected function renderSubtreeItemModels($subtreeItemModels) {
//        if (!$subtreeItemModels) {
//            $wrap = '';
//        } else {
//            $itemTags = [];
//            $first = true;
//            foreach ($subtreeItemModels as $itemDepth => $itemView) {
//                /** @var ItemViewModelInterface $itemView */
//                $itemDepth = $itemView->getData()->getRealDepth();
//                if ($first) {
//                    $rootDepth = $itemDepth;
//                    $currDepth = $itemDepth;
//                    $first = false;
//                }
//                if ($itemDepth>$currDepth) {
//                    $itemStack[$itemDepth][] = $itemView;
//                    $currDepth = $itemDepth;
//                } elseif ($itemDepth<$currDepth) {
//                    $this->renderStackedItems($currDepth, $itemDepth, $itemStack);
//                    $itemStack[$itemDepth][] = $itemView;
//                    $currDepth = $itemDepth;
//                } else {
//                    $itemStack[$currDepth][] = $itemView;
//                }
//            }
//            $this->renderStackedItems($currDepth, $rootDepth, $itemStack);
//            $wrap = $this->renderLastLevel($itemStack[$rootDepth]);
//        }
//        return $wrap;
//    }
//
//    private function renderStackedItems($currDepth, $targetDepth, &$itemStack) {
//        for ($i=$currDepth; $i>$targetDepth; $i--) {
//            $level = [];
//            foreach ($itemStack[$i] as $stackedItemModel) {
//                /** @var ItemViewModelInterface $stackedItemModel */
////                $level[] = $this->itemRenderer->render($stackedItemModel);
//                $level[] = $stackedItemModel;
//            }
//            $wrap = $this->levelWrapRenderer->render(implode(PHP_EOL, $level));
//            unset($itemStack[$i]);
//            end($itemStack[$i-1])->getData()->setInnerHtml($wrap);
//        }
//    }
//
//    private function renderLastLevel($itemStack) {
//        $level = [];
//        foreach ($itemStack as $stackedItemModel) {
//            /** @var ItemViewModelInterface $stackedItemModel */
////            $level[] = $this->itemRenderer->render($stackedItemModel);
//            $level[] = $stackedItemModel;
//        }
//        $wrap = implode(PHP_EOL, $level);                // nejvyšší úroveň stromu je renderována je do "li", "ul" pak udělá menuWrapRenderer, který je nastaven jako renderer celé komponenty ($this->renderer)
//        return $wrap;
//    }
}
