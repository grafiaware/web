<?php
namespace Component\View\Authored\SelectPaperTemplate;

use Pes\View\Template\PhpTemplate;
use Pes\View\CompositeViewInterface;

use Component\View\Authored\AuthoredComponentAbstract;
use Component\ViewModel\Authored\Paper\PaperViewModelInterface;
use Component\Renderer\Html\Authored\Paper\PaperRenderer;
use Component\Renderer\Html\Authored\EmptyContentRenderer;

use Component\Renderer\Html\Authored\Paper\HeadlineRenderer;
use Component\Renderer\Html\Authored\Paper\PerexRenderer;
use Component\Renderer\Html\Authored\Paper\ContentsRenderer;

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
                    // konstruktor PhpTemplate vyhazuje výjimku NoTemplateFileException pro neexistující (nečitený) soubor s template
                    $template = new PhpTemplate($this->getTemplateFileFullname($this->configuration->getTemplatepathPaper(), $this->paperTemplateName));
                    $templatedView = $this->createComponentViewWithTemplate($template);

                    $this->setRendererName(PaperRenderer::class);
                    $this->addChildComponents($templatedView);
                    $this->appendComponentView($templatedView, 'template');
                    
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

    private function addChildComponents(CompositeViewInterface $view) {
        // renderery musí být definovány v Renderer kontejneru - tam mohou dostat classMap do konstruktoru
        $view->appendComponentView($this->createComponentViewWithRenderer(HeadlineRenderer::class), 'headline');
        $view->appendComponentView($this->createComponentViewWithRenderer(PerexRenderer::class), 'perex');
        $view->appendComponentView($this->createComponentViewWithRenderer(ContentsRenderer::class), 'contents');
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