<?php
namespace Web\Component\View\Content\Authored\PaperTemplate;

use Pes\View\Template\PhpTemplate;
use Pes\View\CompositeViewInterface;

use Web\Component\View\Content\Authored\AuthoredComponentAbstract;
use Web\Component\ViewModel\Content\Authored\Paper\PaperViewModelInterface;
use Web\Component\Renderer\Html\Content\Authored\Paper\PaperRenderer;
use Web\Component\Renderer\Html\Content\Authored\EmptyContentRenderer;

use Web\Component\Renderer\Html\Content\Authored\Paper\HeadlineRenderer;
use Web\Component\Renderer\Html\Content\Authored\Paper\PerexRenderer;
use Web\Component\Renderer\Html\Content\Authored\Paper\SectionsRenderer;
// zde jsou definovány konstanty se jmény proměnných pro template (headline, presex, sections) - společné pro PaperComponent
use Web\Component\View\Content\Authored\Paper\PaperComponent;

/**
 * Description of PaperComponent
 *
 * @author pes2704
 */
class PaperTemplateComponent extends AuthoredComponentAbstract implements PaperTemplateComponentInterface {

    /**
     *
     * @var PaperViewModelInterface
     */
    protected $contextData;

    private $selectedPaperTemplateFileName;

    /**
     * Přetěžuje metodu View. Generuje PHP template z názvu template objektu Paper a použije ji.
     */
    public function beforeRenderingHook(): void {
        $paperAggregate = $this->contextData->getPaper();
        if (isset($paperAggregate)) {
            if ($this->selectedPaperTemplateFileName) {
                try {
                    // konstruktor PhpTemplate vyhazuje výjimku NoTemplateFileException pro neexistující (nečitený) soubor s template
                    $template = new PhpTemplate($this->selectedPaperTemplateFileName);
                    $contentView = (new CompositeView())->setData($this->contextData)->setTemplate($template)->setRendererContainer($this->rendererContainer);
                    $this->setRendererName(PaperRenderer::class);
                    $this->addChildComponents($contentView);
                    $this->appendComponentView($contentView, PaperComponent::CONTENT);

                } catch (NoTemplateFileException $noTemplExc) {
                    throw new LogicException("Nelze vytvořit objekt template pro jméno nastavené metodou setPaperTemplateName()? {$this->selectedPaperTemplateFileName}");
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
        $view->appendComponentView((new CompositeView())->setData($this->contextData)->setRendererName(HeadlineRenderer::class)->setRendererContainer($this->rendererContainer), PaperComponent::HEADLINE);
        $view->appendComponentView((new CompositeView())->setData($this->contextData)->setRendererName(PerexRenderer::class)->setRendererContainer($this->rendererContainer), PaperComponent::PEREX);
        $view->appendComponentView((new CompositeView())->setData($this->contextData)->setRendererName(SectionsRenderer::class)->setRendererContainer($this->rendererContainer), PaperComponent::SECTIONS);
    }

    public function setSelectedPaperTemplateFileName($name): void {
        $this->selectedPaperTemplateFileName = $name;
    }

}
