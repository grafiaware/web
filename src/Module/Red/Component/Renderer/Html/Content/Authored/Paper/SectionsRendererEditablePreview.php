<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\Renderer\Html\Content\Authored\Paper;

use Red\Component\Renderer\Html\Content\Authored\Paper\SectionRendererAbstract;

use Red\Component\ViewModel\Content\Authored\Paper\PaperViewModelInterface;

use Red\Model\Entity\PaperAggregatePaperSectionInterface;
use Red\Model\Entity\PaperSectionInterface;
use Red\Middleware\Redactor\Controler\SectionsControler;

use Pes\Text\Html;

/**
 * Description of ContentsRenderer
 *
 * @author pes2704
 */
class SectionsRendererEditablePreview extends SectionRendererAbstract {

    /**
     * Renderuje bloky s atributem id pro TinyMCE jméno proměnné ve formuláři
     *
     * @param MenuItemPaperAggregateInterface $paperAggregate
     * @param type $class
     * @return type
     */
    public function render(iterable $viewModel=NULL) {
        /** @var PaperViewModelInterface $viewModel */
        $paperAggregate = $viewModel->getPaper();
        if ($paperAggregate instanceof PaperAggregatePaperSectionInterface) {
            $contents = $paperAggregate->getPaperSectionsArraySorted(PaperAggregatePaperSectionInterface::BY_PRIORITY);
            $sections = [];
            foreach ($contents as $paperContent) {
                /** @var PaperSectionInterface $paperContent */
                if ($paperContent->getPriority() > 0) {  // není v koši
                    $sections[] = $this->getEditableSectionPreview($paperContent);
                } else {  // je v koši
                    $sections[] = $this->getTrashSectionPrewiew($paperContent);
                }
            }
        } else {
            $sections[] = '';
        }
        return $sections;
    }
}
