<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored;

use Component\Renderer\Html\HtmlRendererAbstract;
use Model\Entity\PaperAggregateInterface;
use Model\Entity\PaperContentInterface;
use Model\Entity\PaperInterface;

use Pes\Text\Html;

/**
 * Description of AuthoredRendererAbstract
 *
 * @author pes2704
 */
abstract class AuthoredRendererAbstract extends HtmlRendererAbstract{

    protected function renderHeadline(PaperAggregateInterface $paper) {
        return  Html::tag('div',
                    ['class'=>$this->classMap->getClass('Headline', 'div')],
                    Html::tag('headline',
                            ['class'=>$this->classMap->getClass('Headline', 'div headline')],
                            $paper->getHeadline()
                    )
                );
    }

    /**
     * Renderuje bloky s atributem id pro TinyMCE jméno proměnné ve formuláři
     *
     * @param MenuItemPaperAggregateInterface $paperAggregate
     * @param type $class
     * @return type
     */
    protected function renderContents(PaperAggregateInterface $paperAggregate) {
        $innerHtml = '';
        foreach ($paperAggregate->getPaperContentsArray() as $paperContent) {
            /** @var PaperContentInterface $paperContent */
            $innerHtml .=
                Html::tag('content', [
                            'id' => "content_{$paperContent->getId()}",
                            'class'=>$this->classMap->getClass('Content', 'content'),
                            'data-owner'=>$paperContent->getEditor()
                        ],
                    $paperContent->getContent()
                );
        }
        return $innerHtml;
    }
}
