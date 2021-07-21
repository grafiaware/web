<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Generated;

use Site\Configuration;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Generated\LanguageSelectViewModel;

use Red\Model\Entity\LanguageInterface;

use Pes\View\Renderer\RendererModelAwareInterface;
use Pes\Text\Html;

/**
 * Description of LanguageSelectRenderer
 *
 * @author pes2704
 */
class LanguageSelectRenderer extends HtmlRendererAbstract {

    public function render($viewModel=NULL) {
        /** @var LanguageSelectViewModel $viewModel */
        $presentedLangCode = $viewModel->getPresentedLangCode();
        $path = Configuration::languageSelectRenderer()['assets'];
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
        return Html::tag('form', ['method'=>'POST', 'action'=>'red/v1/presentation/language'], implode(PHP_EOL, $buttons));
    }

}
