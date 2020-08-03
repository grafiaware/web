<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored;

use Model\Entity\PaperAggregateInterface;

/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
abstract class PaperRendererAbstract extends AuthoredRendererAbstract {

    protected function renderPaper(PaperAggregateInterface $paperAggregate) {
        return
            $innerHtml =
                $this->renderHeadline($paperAggregate).
                $this->renderPerex($paperAggregate).
                $this->renderContents($paperAggregate).
                    "";
    }

}
