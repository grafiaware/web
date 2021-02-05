<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Generated;

use Site\Configuration;

use Component\Renderer\Html\HtmlModelRendererAbstract;
use Component\ViewModel\Generated\LanguageSelectViewModel;

use Model\Entity\LanguageInterface;

use Pes\View\Renderer\RendererModelAwareInterface;
use Pes\Text\Html;

/**
 * Description of LanguageSelectRenderer
 *
 * @author pes2704
 */
class LanguageSelectRenderer extends HtmlModelRendererAbstract implements RendererModelAwareInterface {

    public function render($data=NULL) {
        /** @var LanguageSelectViewModel $viewModel */
        $viewModel = $this->viewModel;
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
        return Html::tag('form', ['method'=>'POST', 'action'=>'api/v1/presentation/language'], implode(PHP_EOL, $buttons));
    }

}
