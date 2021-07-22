<?php
namespace Component\View\Authored\SelectPaperTemplate;

use Pes\View\Template\PhpTemplate;
use Pes\View\Template\ImplodeTemplate;

use Component\View\Authored\AuthoredComponentAbstract;
use Component\ViewModel\Authored\Paper\PaperViewModelInterface;

use Component\Renderer\Html\Authored\Paper\PaperRenderer;

/**
 * Description of PaperComponent
 *
 * @author pes2704
 */
class SelectedPaperTemplateComponent extends AuthoredComponentAbstract implements SelectedPaperTemplateComponentInterface {

    /**
     *
     * @var PaperViewModelInterface
     */
    protected $contextData;

    private $paperTemplateName;

    /**
     * Přetěžuje metodu View. Generuje PHP template z názvu template objektu Paper a použije ji.
     */
    public function beforeRenderingHook(): void {
        $paperAggregate = $this->contextData->getPaper();
        if ($this->paperTemplateName) {
            try {
                $templatePath = $this->getTemplateFileFullname($this->configuration->getTemplatepathPaper(), $this->paperTemplateName);
                $template = new PhpTemplate($templatePath);  // exception
                // renderery musí být definovány v Renderer kontejneru - tam mohou dostat classMap do konstruktoru
//                $this->addChildRendererName('headline', HeadlineRenderer::class);
//                $this->adoptChildRenderers($template);   // jako shared data do template objektu
                $this->setTemplate($template);
            } catch (NoTemplateFileException $noTemplExc) {
                user_error("Neexistuje soubor šablony '{$this->getTemplateFileFullname($this->paperTemplateName)}'", E_USER_WARNING);
                $this->setTemplate(new ImplodeTemplate());
            }
        } else {
            $this->setTemplate(new ImplodeTemplate());
        }

        if ($this->hasContent()) {
            $this->setRendererName(PaperRenderer::class);
        } else {
            $this->setRendererName(EmptyContentRenderer::class);
        }

    }

    public function setPaperTemplateName($name): void {
        $this->paperTemplateName = $name;
    }

    /**
     * Informuje. jestli paper má zobrazitelný obsah. Za paper se zobrazizelným obsahem považuje takový, který má alespoň neprázdný titulek nebo nebo neprázdný perex nebo nastavenou šablonu.
     *
     * Zbývá možnost, že paper má prázdný titulek i perex a nemá šablonu nebo šablona je prázdný soubor, ale má neprázný aper content, takovou možnost metoda neověřuje.
     *
     * @return bool
     */
    private function hasContent(): bool {
        $paper = $this->contextData->getPaper();
        return isset($paper) ? true : false;
    }
}
