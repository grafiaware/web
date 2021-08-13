<?php
namespace Component\View\Authored\SelectPaperTemplate;

use Pes\View\Template\PhpTemplate;

use Component\View\Authored\AuthoredComponentAbstract;
use Component\ViewModel\Authored\Paper\PaperViewModelInterface;

use Component\Renderer\Html\Authored\Paper\HeadlineRenderer;
use Component\Renderer\Html\Authored\Paper\PerexRenderer;
use Component\Renderer\Html\Authored\Paper\ContentsRenderer;
use Component\Renderer\Html\Authored\EmptyContentRenderer;

use Component\Template\PaperTemplate;

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
        if ($this->hasContent()) {
            $paperAggregate = $this->contextData->getPaper();
            if ($this->paperTemplateName) {
                try {
                    $templateFileName = $this->getTemplateFileFullname($this->configuration->getTemplatepathPaper(), $this->paperTemplateName);
                    // renderery musí být definovány v Renderer kontejneru - tam mohou dostat classMap do konstruktoru
                    $this->setTemplate(new PaperTemplate($templateFileName));  // PhpTemplate exception
                    $this->addChildComponentWithRenderer(HeadlineRenderer::class, 'headline');
                    $this->addChildComponentWithRenderer(PerexRenderer::class, 'perex');
                    $this->addChildComponentWithRenderer(ContentsRenderer::class, 'contents');
                } catch (NoTemplateFileException $noTemplExc) {
                    throw new LogicException("Nelze vytvořit objekt template pro jméno nastavené metodou setPaperTemplateName()? {$this->paperTemplateName}");
                }
            } else {
                throw new LogicException("Nebyla nastavena template metodou setPaperTemplateName()");
            }
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
