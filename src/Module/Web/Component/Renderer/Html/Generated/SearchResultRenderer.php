<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Web\Component\Renderer\Html\Generated;

use Web\Component\Renderer\Html\HtmlRendererAbstract;
use Web\Component\ViewModel\Generated\SearchResultViewModel;
use Red\Model\Entity\MenuItemInterface;

use Pes\View\Renderer\RendererModelAwareInterface;
use Pes\Text\Html;
use Pes\Text\Message;

/**
 * Description of LanguageSelectRenderer
 *
 * @author pes2704
 */
class SearchResultRenderer extends HtmlRendererAbstract implements RendererModelAwareInterface {

    public function render(iterable $viewModel=NULL) {
        /** @var SearchResultViewModel $viewModel */
        $html = '';
        $n = 0;
        foreach($viewModel->getSearchedMenuItems() as $menuItem) {
            /** @var MenuItemInterface $menuItem */
            $html .= Html::tag('p', [],
                        ++$n
                        .Html::tag('a', ['href'=>"web/v1/page/item/{$menuItem->getLangCodeFk()}/{$menuItem->getUidFk()}"], $menuItem->getTitle())
                    );
        }
        if ( $n== 0) {
            $html .= Html::tag('p', [],
                            Message::t('Nebyly nalezeny žádné záznamy obsahující: {query}.', ['query'=>$viewModel->getQuery()])
                        );
        }
        return $html;
    }

}