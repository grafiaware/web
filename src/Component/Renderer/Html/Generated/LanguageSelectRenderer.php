<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Generated;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Generated\LanguageSelectViewModel;

use Model\Entity\LanguageInterface;

use Pes\Text\Html;

/**
 * Description of LanguageSelectRenderer
 *
 * @author pes2704
 */
class LanguageSelectRenderer extends HtmlRendererAbstract {

    private $flagsPath = "flags-mini/";

    public function render($data=NULL) {
        return $this->renderPrivate($data);
    }

    private function renderPrivate(LanguageSelectViewModel $viewModel) {
        $presentedLangCode = $viewModel->getPresentedLangCode();
        $path = \Middleware\Web\AppContext::getAppPublicDirectory().$this->flagsPath;
        foreach ($viewModel->getLanguages() as $language) {
            $flagfile = $path.$language->getState().".png";
            /* @var $language LanguageInterface */
            $langCode = $language->getLangCode();
            $buttons[] = Html::tag('button', [
                            'name'=>'langcode',
                            'value'=>$langCode,
                            'class'=>$this->classMap->resolveClass($langCode == $presentedLangCode , 'Item', 'button.presentedlanguage', 'button'),
                        ],
                        Html::tagNopair('img', [
                            'src'=>$flagfile,
                            'class'=>$this->classMap->resolveClass($langCode == $presentedLangCode , 'Item', 'img.presentedlanguage', 'img'),
                            'alt'=>$language->getName()
                            ])
                        );
        }
        return Html::tag('form', ['method'=>'POST', 'action'=>'api/v1/presentation/language'], implode(PHP_EOL, $buttons));
    }

}
