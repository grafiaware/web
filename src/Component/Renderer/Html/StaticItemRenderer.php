<?php

namespace Component\Renderer\Html;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\StaticItemViewModelInterface;
use Pes\Core\Text\Html;
use Pes\View\Renderer\ClassMap\ClassMapInterface;
use Red\Middleware\Redactor\Controler\StaticControler;
use StaticRegistry\Service\StaticTemplateLocation;
use Template\Compiler\TemplateCompilerInterface;

/**
 * Renderer static stránky: v edit módu formulář path/template, pak PHP šablona.
 *
 * Pokud getTemplateOptions() vrátí seznam z remote modulu, zobrazí jeden select
 * (path i template v jedné hodnotě). Jinak textová pole (red static, nebo remote list nedostupný).
 *
 * @author pes2704
 */
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
            $options = $viewModel->getTemplateOptions();
            $fields = $options === []
                ? $this->renderPathField($viewModel) . $this->renderTemplateField($viewModel)
                : $this->renderLocationSelect($viewModel, $options);
            $html = 
                Html::tag("div", ["class"=>"ui blue segment"],
                    Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/static/$id"],
                        Html::tag('p', [], "Parametry static stránky:")
                        . $fields
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

    /** Textová pole, když seznam šablon z auth/events není k dispozici (red static, nebo remote list selhal). */
    private function renderPathField(StaticItemViewModelInterface $viewModel): string {
        return Html::input(
            StaticControler::PATH_VAR_NAME,
            "Path: ",
            [StaticControler::PATH_VAR_NAME => $viewModel->getStaticItemPath()]
        );
    }

    private function renderTemplateField(StaticItemViewModelInterface $viewModel): string {
        return Html::input(
            StaticControler::TEMPLATE_VAR_NAME,
            "Template: ",
            [StaticControler::TEMPLATE_VAR_NAME => $viewModel->getStaticItemTemplate()]
        );
    }

    /**
     * Jeden select ukládá path i template najednou.
     * Dva oddělené selecty posílaly prázdný template (scanner ho neměl) a path, které nesedělo na uloženou hodnotu,
     * takže odeslání formuláře přepsalo path prázdným řetězcem.
     *
     * @param array<int, array{path: string, template: string, fullTemplatePath?: string}> $options
     */
    private function renderLocationSelect(StaticItemViewModelInterface $viewModel, array $options): string {
        $currentPath = $viewModel->getStaticItemPath();
        $currentTemplate = $viewModel->getStaticItemTemplate();
        $currentValue = StaticTemplateLocation::encode($currentPath, $currentTemplate);
        $selectOptions = [];
        $currentListed = false;
        foreach ($options as $option) {
            $path = (string) ($option['path'] ?? '');
            $template = (string) ($option['template'] ?? '');
            $value = StaticTemplateLocation::encode($path, $template);
            if ($value === '') {
                continue;
            }
            $selectOptions[$value] = $path . $template;
            if ($value === $currentValue) {
                $currentListed = true;
            }
        }
        if (!$currentListed) {
            $selectOptions = [$currentValue => 'aktuální: ' . $currentPath . $currentTemplate] + $selectOptions;
        }
        $optionsHtml = '';
        foreach ($selectOptions as $value => $text) {
            $selected = ((string) $value === $currentValue) ? ' selected="selected"' : '';
            $optionsHtml .= '<option value="' . htmlspecialchars((string) $value, ENT_QUOTES) . '"' . $selected . '>'
                . htmlspecialchars($text, ENT_QUOTES) . '</option>';
        }
        return Html::tag('label', [], 'Šablona: ')
            . Html::tag('select', [
                'name' => StaticControler::LOCATION_VAR_NAME,
                'id' => StaticControler::LOCATION_VAR_NAME,
            ], $optionsHtml);
    }
}
