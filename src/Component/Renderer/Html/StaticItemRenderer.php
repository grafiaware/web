<?php

namespace Component\Renderer\Html;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\StaticItemViewModelInterface;
use Pes\Core\Text\Html;
use Pes\View\Renderer\ClassMap\ClassMapInterface;
use Red\Middleware\Redactor\Controler\StaticControler;
use Template\Compiler\TemplateCompilerInterface;

class StaticItemRenderer extends HtmlRendererAbstract {
    
    private TemplateCompilerInterface $templateCompiler;
    
    public function __construct(
            TemplateCompilerInterface $templateCompiler, 
            ?ClassMapInterface $menuClassMap = NULL) {
        parent::__construct($menuClassMap);
        $this->templateCompiler = $templateCompiler;
    }
    
    public function render(?iterable $viewModel = NULL) {
        /** @var StaticItemViewModelInterface $viewModel */
        $html = '';
        if ($viewModel->isEditable()) {
            $id = $viewModel->getStaticItemId();
            $html = 
                Html::tag("div", ["class"=>"ui blue segment"],
                    Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/static/$id"],
                        Html::tag('p', [], "Parametry static stránky:")
                        . $this->renderPathField($viewModel)
                        . $this->renderTemplateField($viewModel)
                        .Html::tag('p', [])
                        .Html::tag("div", [],
                            Html::tag("button", [], "Odeslat")
                        )
                    )
                );
        }
        $this->templateCompiler->injectTemplateVars(iterator_to_array($viewModel));
        $html .= $this->templateCompiler->getCompiledContent($viewModel->getStaticFullTemplatePath());
        return $html;
    }

    private function renderPathField(StaticItemViewModelInterface $viewModel): string {
        $options = $viewModel->getTemplateOptions();
        if ($options === []) {
            return Html::input(
                StaticControler::PATH_VAR_NAME,
                "Path: ",
                [StaticControler::PATH_VAR_NAME => $viewModel->getStaticItemPath()]
            );
        }
        return $this->renderTemplateSelect(
            StaticControler::PATH_VAR_NAME,
            'Path: ',
            $viewModel->getStaticItemPath(),
            $options,
            'path'
        );
    }

    private function renderTemplateField(StaticItemViewModelInterface $viewModel): string {
        $options = $viewModel->getTemplateOptions();
        if ($options === []) {
            return Html::input(
                StaticControler::TEMPLATE_VAR_NAME,
                "Template: ",
                [StaticControler::TEMPLATE_VAR_NAME => $viewModel->getStaticItemTemplate()]
            );
        }
        return $this->renderTemplateSelect(
            StaticControler::TEMPLATE_VAR_NAME,
            'Template: ',
            $viewModel->getStaticItemTemplate(),
            $options,
            'template'
        );
    }

    /**
     * @param array<int, array{path: string, template: string, fullTemplatePath?: string}> $options
     */
    private function renderTemplateSelect(string $fieldName, string $label, string $currentValue, array $options, string $valueKey): string {
        $selectOptions = ['' => '— vyberte —'];
        foreach ($options as $option) {
            $value = (string) ($option[$valueKey] ?? '');
            $labelText = ($option['path'] ?? '') . ($option['template'] ?? '');
            if (($option['fullTemplatePath'] ?? '') !== '') {
                $labelText = $option['fullTemplatePath'];
            }
            $selectOptions[$value] = $labelText;
        }
        $attributes = ['name' => $fieldName, 'id' => $fieldName];
        $optionsHtml = '';
        foreach ($selectOptions as $value => $text) {
            $selected = ((string) $value === $currentValue) ? ' selected="selected"' : '';
            $optionsHtml .= '<option value="' . htmlspecialchars((string) $value, ENT_QUOTES) . '"' . $selected . '>'
                . htmlspecialchars($text, ENT_QUOTES) . '</option>';
        }
        return Html::tag('label', [], $label)
            . Html::tag('select', $attributes, $optionsHtml);
    }
}
