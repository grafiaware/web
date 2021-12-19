<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored\Paper;

use Pes\View\Renderer\ClassMap\ClassMapInterface;

use Red\Model\Entity\PaperAggregatePaperContentInterface;
use Red\Model\Entity\PaperInterface;
use Red\Model\Entity\PaperContentInterface;

use Pes\Text\Html;

/**
 * Description of AuthoredEditableRenderer
 *
 * @author pes2704
 */
class ElementWrapper {

    /**
     *
     * @var ClassMapInterface
     */
    protected $classMap;

    /**
     *
     * @param ClassMapInterface $menuClassMap
     */
    public function __construct(ClassMapInterface $menuClassMap=NULL) {
        $this->classMap = $menuClassMap;
    }

    public function wrapHeadline(PaperInterface $paper) {
        $headline = $paper->getHeadline();
        return Html::tag('headline',
                            ['class'=>$this->classMap->get('Headline', 'headline')],
                            $headline ?? ''
                    );
    }

    public function wrapPerex(PaperInterface $paper) {
        $perex = $paper->getPerex();
        return  Html::tag('perex',
                    ['class'=>$this->classMap->get('Perex', 'perex')],
                    $perex ?? ''
                );
    }

    public function wrapContent(PaperContentInterface $paperContent) {
        return  Html::tag('content', [
                            'id' => "content_{$paperContent->getId()}",
                            'class'=>$this->classMap->get('Content', 'content'),
                            'data-owner'=>$paperContent->getEditor()
                        ],
                    $paperContent->getContent()
                );
    }

}
