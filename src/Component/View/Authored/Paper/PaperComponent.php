<?php
namespace Component\View\Authored\Paper;

use Pes\View\Renderer\RendererInterface;
use Pes\View\Template\PhpTemplate;

use Component\View\Authored\Paper\AuthoredComponentAbstract;
use Component\View\Authored\Paper\Article\ArticleComponentInterface;

use Component\ViewModel\Authored\Paper\PaperViewModelInterface;

/**
 * Description of NamedItemComponent
 *
 * @author pes2704
 */
class PaperComponent extends AuthoredComponentAbstract implements PaperComponentInterface {

//            if (!isset($paperAggregate)) {
//                $paperAggregate = $viewModel->getNewPaper();  // vrací Paper
//            }

    /**
     * Přetěžuje metodu View. Generuje PHP template z názvu template objektu Paper a použije ji.
     */
    public function beforeRenderingHook(): void {
        $paperAggregate = $this->contextData->getPaper();
        if (isset($paperAggregate)) {
            $templateName = $paperAggregate->getTemplate();
            if (isset($templateName) AND $templateName) {
                $templatePath = $this->getTemplatePath($templateName);
                try {
                    $template = new PhpTemplate($templatePath);  // exception
//                    $parentRendererClassMap = parent::resolveRenderer()->getClassMap();
                    $sharedData = [];
                    if (isset($this->rendererContainer)) {
                        foreach ($this->templateGlobals as $variableName => $rendererName) {
                            $sharedData[$variableName] = $this->rendererContainer->get($rendererName);
                        }
                    }
                    $template->setSharedData($sharedData);
                    $this->setTemplate($template);
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


        return ;
    }
}
