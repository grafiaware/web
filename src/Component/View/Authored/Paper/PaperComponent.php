<?php
namespace Component\View\Authored\Paper;

use Pes\View\Template\ImplodeTemplate;
use Pes\View\View;

use Red\Model\Entity\PaperAggregatePaperContentInterface;

use Component\View\Authored\AuthoredComponentAbstract;
use Component\ViewModel\Authored\Paper\PaperViewModelInterface;

use Component\Renderer\Html\Authored\Paper\PaperRenderer;
use Component\Renderer\Html\Authored\Paper\PaperRendererEditable;
use Component\Renderer\Html\Authored\EmptyContentRenderer;

use Component\Template\PaperTemplate;
use Component\Template\PaperTemplateEditable;

use Component\View\Authored\AuthoredElement;
use Component\Renderer\Html\Authored\Paper\SelectPaperTemplateRenderer;
use Component\Renderer\Html\Authored\Paper\HeadlineRenderer;
use Component\Renderer\Html\Authored\Paper\PerexRenderer;
use Component\Renderer\Html\Authored\Paper\ContentsRenderer;
use Component\Renderer\Html\Authored\Paper\HeadlineRendererEditable;
use Component\Renderer\Html\Authored\Paper\PerexRendererEditable;
use Component\Renderer\Html\Authored\Paper\ContentsRendererEditable;

/**
 * Description of PaperComponent
 *
 * @author pes2704
 */
class PaperComponent extends AuthoredComponentAbstract implements PaperComponentInterface {

    /**
     *
     * @var PaperViewModelInterface
     */
    protected $contextData;

    /**
     * Přetěžuje metodu View. Generuje PHP template z názvu template objektu Paper a použije ji.
     */
    public function beforeRenderingHook(): void {
        if ($this->hasContent()) {
            $paperAggregate = $this->contextData->getPaper();
            $templateFileName = $this->getTemplateFileFullname($this->configuration->getTemplatepathPaper(), $this->getTemplate());

            try {
                if ($this->contextData->userCanEdit()) { // editační režim a uživatel má právo editovat
                    $this->setTemplate(new PaperTemplateEditable($templateFileName));  // PhpTemplate exception
                    $this->adoptComponentView(SelectPaperTemplateRenderer::class, 'selectTemplate');
                    $this->adoptComponentView(HeadlineRendererEditable::class, 'headline');
                    $this->adoptComponentView(PerexRendererEditable::class, 'perex');
                    if ($paperAggregate instanceof PaperAggregatePaperContentInterface) {
                        $this->adoptComponentView(ContentsRendererEditable::class, 'contents');
                    } else {
                        $this->adoptComponentView(EmptyContentRenderer::class, 'contents');
                    }
                } else {
                    $this->setTemplate(new PaperTemplate($templateFileName));  // PhpTemplate exception
                    $this->adoptComponentView(HeadlineRenderer::class, 'headline');
                    $this->adoptComponentView(PerexRenderer::class, 'perex');
                    if ($paperAggregate instanceof PaperAggregatePaperContentInterface) {
                        $this->adoptComponentView(ContentsRenderer::class, 'contents');
                    }
                }
                // renderery musí být definovány v Renderer kontejneru - tam mohou dostat classMap do konstruktoru
//                $this->addChildRendererName('headline', HeadlineRenderer::class);
//                $this->adoptChildRenderers($template);   // jako shared data do template objektu
            } catch (NoTemplateFileException $noTemplExc) {
                user_error("Neexistuje soubor šablony '$templatePath'", E_USER_WARNING);
                $this->setTemplate(new PaperTemplate($this->getTemplateFileFullname($this->configuration->getTemplatepathPaper(), self::DEFAULT_TEMPLATE_NAME)));  // PhpTemplate exception - nezachycená
            }
        } else {
            $this->setRendererName(EmptyContentRenderer::class);
        }
    }

    private function getTemplate() {
        $template = $this->contextData->getPaper()->getTemplate();
        return (isset($template) AND $template) ? $template : self::DEFAULT_TEMPLATE_NAME;
    }

    /**
     * Informuje. jestli paper má zobrazitelný obsah. Za paper se zobrazizelným obsahem považuje takový, který má alespoň neprázdný titulek nebo nebo neprázdný perex nebo nastavenou šablonu.
     *
     * Zbývá možnost, že paper má prázdný titulek i perex a nemá šablonu nebo šablona je prázdný soubor, ale má neprázný aper content, takovou možnost metoda neověřuje.
     *
     * @return bool
     */
    public function hasContent(): bool {
        $paper = $this->contextData->getPaper();
        return isset($paper) ? true : false;
    }
}
