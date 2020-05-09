<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Generated;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Generated\SearchPhraseViewModel;

use Pes\Text\Message;

/**
 * Description of SearchPhraseRenderer
 *
 * @author pes2704
 */
class SearchPhraseRenderer extends HtmlRendererAbstract {
    public function render($data=NULL) {
        return $this->renderPrivate($data);
    }

    private function renderPrivate() {  // bez parametru - nepoužívá viewModel
        // input - musí být required, jinak se při nulové délce neprovádí validace a pošle se dotaz s prázdným řetězcem
        return '
            <form action="www/searchresult" method="GET">
            <div class="ui mini action fluid input">
            <input placeholder="'.Message::t("Vyhledat...").'" type="search" name="klic" value=""  minlength="3" maxlength="200" required>
            <button class="ui icon button" type="submit"><i class="search link icon"></i></button>
            </div>
            </form>';
    }
}
