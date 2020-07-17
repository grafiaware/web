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
                            ['class'=>$this->classMap->getClass('Headline', 'headline')],
                            $paper->getHeadline()
                    )
                );
    }

    protected function renderPerex(PaperAggregateInterface $paper) {
        return  Html::tag('perex',
                            ['class'=>$this->classMap->getClass('Perex', 'perex')],
                            $paper->getPerex()
                    )
                ;
    }

    /**
     * Compare funkce pro usort - řadí shora od nejvyšší priority
     *
     * @param PaperContentInterface $c1
     * @param PaperContentInterface $c2
     * @return int
     */
    private function compareByPriority($c1, $c2) {
        /** @var PaperContentInterface $c1 */
        /** @var PaperContentInterface $c2 */
        if ($c1->getPriority() == $c2->getPriority()) {
            return 0;
        }
        // desc !
        return ($c1->getPriority() > $c2->getPriority()) ? -1 : 1;
    }

    /**
     * Renderuje bloky s atributem id pro TinyMCE jméno proměnné ve formuláři
     *
     * @param MenuItemPaperAggregateInterface $paperAggregate
     * @param type $class
     * @return type
     */
    protected function renderContents(PaperAggregateInterface $paperAggregate) {
        $contents = $paperAggregate->getPaperContentsArray();
        \usort($contents, array($this, "compareByPriority"));
        $innerHtml = '';
        foreach ($contents as $paperContent) {
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
