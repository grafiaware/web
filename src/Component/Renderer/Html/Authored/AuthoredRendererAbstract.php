<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored;

use Component\Renderer\Html\HtmlRendererAbstract;
use Model\Entity\PaperInterface;
use Model\Entity\PaperContentInterface;
use Model\Entity\PaperHeadlineInterface;

use Pes\Text\Html;

/**
 * Description of AuthoredRendererAbstract
 *
 * @author pes2704
 */
abstract class AuthoredRendererAbstract extends HtmlRendererAbstract{

    protected function renderHeadline(PaperInterface $paper) {
        return  Html::tag('div',
                    ['class'=>$this->classMap->getClass('Component', 'div div')],
                    Html::tag('headline',
                            ['class'=>$this->classMap->getClass('Component', 'div div headline')],
                            $paper->getHeadline()->getHeadline()
                    )
                );
    }

    /**
     * Renderuje bloky s atributem id pro TinyMCE jméno proměnné ve formuláři
     *
     * @param PaperInterface $paper
     * @param type $class
     * @return type
     */
    protected function renderContents(PaperInterface $paper) {
        $innerHtml = '';
        foreach ($paper->getContents() as $paperContent) {
            /** @var PaperContentInterface $paperContent */
            $innerHtml .=
                Html::tag('block', [
                            'id' => "content_{$paperContent->getId()}",
//                            'class'=> $this->classMap->getClass('Component', 'div block'),    // classmap 'paper.block.classmap'
                            'class'=>$this->classMap->getClass('Component', 'div div content'),
                            'data-owner'=>$paperContent->getEditor()
                        ],
                    $paperContent->getContent()
                );
        }
        return $innerHtml;
    }
}
