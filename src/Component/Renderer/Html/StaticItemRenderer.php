<?php

namespace Component\Renderer\Html;

use Component\Renderer\Html\HtmlRendererAbstract;
use Pes\View\Renderer\ClassMap\ClassMapInterface;
use Component\ViewModel\StaticItemViewModelInterface;
use Pes\Text\Html;
use View\Includer;
use Template\Compiler\TemplateCompilerInterface;
use Red\Middleware\Redactor\Controler\StaticControler;

/**
 * Description of StaticItemRenderer
 *
 * @author pes2704
 */
class StaticItemRenderer extends HtmlRendererAbstract {
    
    private $templateCompiler;
    
    public function __construct(
            TemplateCompilerInterface $templateCompiler, 
            ClassMapInterface $menuClassMap = NULL) {
        parent::__construct($menuClassMap);
        $this->templateCompiler = $templateCompiler;
    }
    
    public function render(iterable $viewModel = NULL) {
        /** @var StaticItemViewModelInterface $viewModel */
        $html = '';
        if ($viewModel->isEditable()) {
            $id = $viewModel->getStaticItemId();
            $html = 
                Html::tag("div", ["class"=>"ui blue segment"],
                    Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/static/$id"],
                        Html::tag('p', [], "Parametry static stránky:")
                        .Html::input(StaticControler::PATH_VAR_NAME, "Path: ", [StaticControler::PATH_VAR_NAME=>$viewModel->getStaticItemPath()])
                        .Html::input(StaticControler::TEMPLATE_VAR_NAME, "Template: ", [StaticControler::TEMPLATE_VAR_NAME=>$viewModel->getStaticItemTemplate()])
                        .Html::tag('p', [])
                        .Html::tag("div", [],
                            Html::tag("button", [], "Odeslat")
                        )
                    )
                );
        }
        $this->templateCompiler->injectTemplateVars(iterator_to_array($viewModel));
        $html .= $this->templateCompiler->getCompiledContent($viewModel->getStaticFullTemplatePath());
        //->protectedIncludeScope($viewModel->getStaticFullTemplatePath(), iterator_to_array($viewModel)); //? kontejner
        return $html;
    }
}
