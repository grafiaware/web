<?php
namespace Component\View\Authored\Paper;

use Pes\View\Template\PhpTemplate;

use Component\View\Authored\AuthoredComponentAbstract;
use Component\ViewModel\Authored\Paper\PaperViewModelInterface;

use Component\Renderer\Html\Authored\Paper\PaperRenderer;
use Component\Renderer\Html\Authored\Paper\PaperRendererEditable;

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
        $paperAggregate = $this->contextData->getPaper();
        if ($this->getContentTemplateName()) {
            try {
                $templatePath = $this->getTemplateFileFullname($this->configuration->getTemplatepathPaper(), $paperAggregate->getTemplate());
                $template = new PhpTemplate($templatePath);  // exception
                // renderery musí být definovány v Renderer kontejneru - tam mohou dostat classMap do konstruktoru
//                $this->addChildRendererName('headline', HeadlineRenderer::class);
//                $this->adoptChildRenderers($template);   // jako shared data do template objektu
                $this->setTemplate($template);
            } catch (NoTemplateFileException $noTemplExc) {
                user_error("Neexistuje soubor šablony '{$this->getTemplateFileFullname($paperAggregate->getTemplate())}'", E_USER_WARNING);
                $this->setTemplate(null);
            }
        }
        if ($this->contextData->userCanEdit() AND !$this->readonly) { // editační režim a uživatel má právo editovat
            if ($this->hasContent()) {
                $this->setRendererName(PaperRendererEditable::class);
            } else {
                $this->setRendererName(EmptyContentRenderer::class);
            }
        } elseif ($this->hasContent()) {
                $this->setRendererName(PaperRenderer::class);
        } else {
            $this->setRendererName(EmptyContentRenderer::class);
        }
    }


    public function getContentTemplateName() {
        $paper = $this->contextData->getPaper();
        return isset($paper) ? $paper->getTemplate() : null;
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
