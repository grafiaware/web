<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Authored\Paper\Article;

use Pes\View\Renderer\RendererInterface;
use Pes\View\Template\PhpTemplate;

use Component\View\Authored\Paper\AuthoredComponentAbstract;
use Component\Renderer\Html\Authored\Buttons;
use Component\Renderer\Html\Authored\ElementWrapper;
/**
 * Description of ArticleComponent
 *
 * @author pes2704
 */
class ContentComponent extends AuthoredComponentAbstract implements ContentComponentInterface {
    /**
     * Přetěžuje metodu View. Generuje PHP template z názvu template objektu Paper a použije ji.
     */
    protected function resolveRenderer(): RendererInterface {
        $paperAggregate = $this->viewModel->getPaper();
        if (isset($paperAggregate)) {
            $paperTemplateName = $paperAggregate->getTemplate();
            if (isset($paperTemplateName) AND $paperTemplateName) {
                $templatePath = $this->templatesPath.$paperTemplateName."/template.php";
                try {
                    $template = new PhpTemplate($templatePath);  // exception
                    $parentRendererClassMap = parent::resolveRenderer()->getClassMap();
                    $data = [];
                    if (isset($this->rendererContainer)) {
                        foreach ($this->templateGlobals as $variableName => $rendererName) {
                            $data[$variableName] = $this->rendererContainer->get($rendererName);
                        }
                    }
                    $template->setSharedData($data);
                    $this->setTemplate($template);
                    $this->setData(['paperAggregate' => $paperAggregate]);
                } catch (NoTemplateFileException $noTemplExc) {
                    user_error("Neexistuje soubor šablony $templatePath", E_USER_WARNING);
                    $this->setTemplate(null);
                }
            }
        }

//        if (isset($template)) {
//            $paperAggregateOverride = clone $paperAggregate;
//            $paperAggregateOverride->setHeadline($renderer->renderHeadline($paperAggregate));
//            $paperAggregateOverride->setPerex($renderer->renderPerex($paperAggregate));
//            $contentsOverride = [];
//            foreach ($paperAggregate->getPaperContentsArraySorted(PaperAggregateInterface::BY_PRIORITY) as $paperContent) {
//                $contentsOverride[] = $renderer->renderContent($paperContent);
//            }
//            $paperAggregateOverride->exchangePaperContentsArray($contentsOverride);
//            $this->viewModel->overridePaperAggregate($paperAggregateOverride);
//        }


//        return isset($renderer) ? $renderer : parent::resolveRenderer();
        return parent::resolveRenderer();
    }
}