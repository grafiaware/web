<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace  Component\Renderer\Html\Authored\Menu;

use Pes\View\Renderer\RendererInterface;
use Component\Renderer\Html\HtmlModelRendererAbstract;
use Component\Renderer\Html\Authored\Menu\LevelWrapRenderer;
use Component\Renderer\Html\Authored\Menu\ItemRenderer;
use Component\ViewModel\Authored\Menu\Item\ItemViewModelInterface;

use Pes\View\Renderer\RendererModelAwareInterface;

/**
 * Description of MenuWrapRndererAbstract
 *
 * @author pes2704
 */
abstract class MenuWrapRendererAbstract extends HtmlModelRendererAbstract implements MenuWrapRendererInterface, RendererModelAwareInterface {

    /**
     * @var LevelWrapRenderer
     */
    private $levelWrapRenderer;

    /**
     * @var ItemRenderer
     */
    private $itemRenderer;

    public function setLevelWrapRenderer(RendererInterface $levelWrapRenderer): void {
        $this->levelWrapRenderer = $levelWrapRenderer;
    }

    public function setItemRenderer(RendererInterface $itemRenderer): void {
        $this->itemRenderer = $itemRenderer;
    }

    protected function getMenuHtml($subtreeItemModels) {
        if (!$subtreeItemModels) {
            $wrap = '';
        } else {
            $itemTags = [];
            $first = true;
            foreach ($subtreeItemModels as $itemModel) {
                /** @var ItemViewModelInterface $itemModel */
                $itemDepth = $itemModel->getRealDepth();
                if ($first) {
                    $rootDepth = $itemDepth;
                    $currDepth = $itemDepth;
                    $first = false;
                }
                if ($itemDepth>$currDepth) {
                    $itemStack[$itemDepth][] = $itemModel;
                    $currDepth = $itemDepth;
                } elseif ($itemDepth<$currDepth) {
                    $this->renderStackedItems($currDepth, $itemDepth, $itemStack);
                    $itemStack[$itemDepth][] = $itemModel;
                    $currDepth = $itemDepth;
                } else {
                    $itemStack[$currDepth][] = $itemModel;
                }
            }
            $this->renderStackedItems($currDepth, $rootDepth, $itemStack);
            $wrap = $this->renderLastLevel($itemStack[$rootDepth]);
        }
        return $wrap;
    }

    private function renderStackedItems($currDepth, $targetDepth, &$itemStack) {
        for ($i=$currDepth; $i>$targetDepth; $i--) {
            $level = [];
            foreach ($itemStack[$i] as $stackedItemModel) {
                /** @var ItemViewModelInterface $stackedItemModel */
                $this->itemRenderer->setViewModel($stackedItemModel);
                $level[] = $this->itemRenderer->render();
            }
            $wrap = $this->levelWrapRenderer->render(implode(PHP_EOL, $level));
            unset($itemStack[$i]);
            end($itemStack[$i-1])->setInnerHtml($wrap);
        }
    }

    private function renderLastLevel($itemStack) {
        $level = [];
        foreach ($itemStack as $stackedItemModel) {
            /** @var ItemViewModelInterface $stackedItemModel */
            $this->itemRenderer->setViewModel($stackedItemModel);
            $level[] = $this->itemRenderer->render();
        }
        $wrap = implode(PHP_EOL, $level);                // nejvyšší úroveň stromu je renderována je do "li", "ul" pak udělá menuWrapRenderer, který je nastaven jako renderer celé komponenty ($this->renderer)
        return $wrap;
    }
}