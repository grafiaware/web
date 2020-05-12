<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Generated;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Generated\SearchResultViewModel;

use Pes\Text\Html;
use Pes\Text\Message;

/**
 * Description of LanguageSelectRenderer
 *
 * @author pes2704
 */
class SearchResultRenderer extends HtmlRendererAbstract {

    public function render($data=NULL) {
        return $this->renderPrivate($data);
    }

    private function renderPrivate(SearchResultViewModel $viewModel) {
        $html = '';
        $n = 0;
        foreach($viewModel->getSearchedMenuItems() as $menuItem) {
            $html .= Html::tag('p', [],
                        ++$n
                        .Html::tag('a', ['href'=>"/www/item/{$menuNode->getMenuItem()->getLangCodeFk()}/{$menuItem->getUidFk()}"], $menuItem->getTitle())
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