<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Authored\Paper\Article;

use Component\View\Authored\Paper\AuthoredComponentAbstract;
use Pes\View\Renderer\RendererInterface;

use Pes\View\Renderer\RendererModelAwareInterface;

/**
 * Description of ArticleComponent
 *
 * @author pes2704
 */
class ArticleComponent extends AuthoredComponentAbstract implements ArticleComponentInterface {
    /**
     * Přetěžuje metodu View. Generuje PHP template z názvu template objektu Paper a použije ji.
     */
    protected function resolveRenderer(): RendererInterface {
        $paperAggregate = $this->viewModel->getPaper();
        if (isset($paperAggregate)) {
            $paperTemplateName = $paperAggregate->getTemplate();
            $template = $this->resolveTemplate($paperTemplateName);
            $this->setTemplate($template);
        }
        $renderer = parent::resolveRenderer();
        if (!($renderer instanceof RendererModelAwareInterface)) {
            if (!is_iterable($this->viewModel)) {
                throw new LogicException("ViewModel ". get_class($this->viewModel)." není iterable. Komponent má nastaven PHP template renderer a vyžaduje iterable view model.");
            }
            $this->data = $this->viewModel;
        }

        return $renderer;
    }
}
