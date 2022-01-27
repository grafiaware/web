<?php
namespace Component\View\Authored\Paper;

use Pes\View\Template\PhpTemplate;
use Pes\View\Template\ImplodeTemplate;
use Pes\View\CompositeViewInterface;

use Pes\View\Template\Exception\NoTemplateFileException;

use Red\Model\Entity\PaperAggregatePaperContentInterface;

use Component\View\Authored\AuthoredComponentAbstract;
use Component\ViewModel\Authored\Paper\PaperViewModelInterface;

use Component\Renderer\Html\Authored\Paper\PaperRenderer;
use Component\Renderer\Html\Authored\Paper\PaperRendererEditable;
use Component\Renderer\Html\Authored\EmptyContentRenderer;

use Component\View\Authored\AuthoredElement;

use Component\Renderer\Html\Authored\Paper\SelectPaperTemplateRenderer;
use Component\Renderer\Html\Authored\Paper\ButtonsRenderer;

use Component\Renderer\Html\Authored\Paper\HeadlineRenderer;
use Component\Renderer\Html\Authored\Paper\PerexRenderer;
use Component\Renderer\Html\Authored\Paper\ContentsRenderer;
use Component\Renderer\Html\Authored\Paper\HeadlineRendererEditable;
use Component\Renderer\Html\Authored\Paper\PerexRendererEditable;
use Component\Renderer\Html\Authored\Paper\ContentsRendererEditable;

use Component\View\Manage\ToggleEditContentButtonComponent;

use Red\Model\Enum\AuthoredEnum;
use Pes\Type\ContextData;

/**
 * Description of PaperComponent
 *
 * @author pes2704
 */
class PaperComponent extends AuthoredComponentAbstract implements PaperComponentInterface {

    const CONTENT = 'template';
    const PEREX = 'perex';
    const HEADLINE = 'headline';
    const PARTS = 'contents';

    const SELECT_TEMPLATE = 'selectTemplate';

    /**
     *
     * @var PaperViewModelInterface
     */
    protected $contextData;

    /**
     * Přetěžuje metodu View. Generuje PHP template z názvu template objektu Paper a použije ji.
     * Pokud soubor template neexistuje, použije soubor default template, pokud ani ten neexistuje, použije PaperRenderer respektive PaperEditableRenderer.
     *
     *
     */
    public function beforeRenderingHook(): void {
        if ($this->hasContent()) {

            // vytvoří komponentní view z šablony paperu nebo s ImplodeTemplate, pokud šablona paperu není nastavena
            try {
                // konstruktor PhpTemplate vyhazuje výjimku NoTemplateFileException pro neexistující (nečitený) soubor s template
                $template = new PhpTemplate($this->contextData->seekTemplate());
            } catch (NoTemplateFileException $noTemplExc) {
                $template = new ImplodeTemplate();
            }
            $contentView = $this->createCompositeViewWithTemplate($template);
            // zvolí PaperRenderer nebo PaperRendererEditable
            if ($this->contextData->presentEditableContent()) { // editační režim
                if ($this->userPerformActionWithItem()) {
                    $this->setRendererName(PaperRendererEditable::class);
                    // připojí k komponentě komponentní view s editable renderery headline, perex, contents
                    $this->addChildEditableComponents($contentView);
                } else {
                    $this->setRendererName(PaperRenderer::class);
                    // připojí k komponentě komponentní view s editable renderery headline, perex, contents
                    $this->addChildComponents($contentView);
                }
                // připojí komponent - view s buttonem ToggleEditContentButtonComponent (tužtička)
                $buttonEditContentComponent = new ToggleEditContentButtonComponent($this->configuration);
                $buttonEditContentComponent->setData($this->contextData);
                $buttonEditContentComponent->setRendererContainer($this->rendererContainer);
                $this->appendComponentView($buttonEditContentComponent, self::BUTTON_EDIT_CONTENT);

            } else {  // needitační režim
                $this->setRendererName(PaperRenderer::class);
                // připojí k templated view komponentní view s editable renderery headline, perex, contents
                $this->addChildComponents($contentView);
            }
            $this->appendComponentView($contentView, self::CONTENT);
        } else {
            $this->setRendererName(EmptyContentRenderer::class);
        }
    }

    public function __toString() {
        parent::__toString();
    }

    private function addChildEditableComponents(CompositeViewInterface $view) {
        // renderery musí být definovány v Renderer kontejneru - tam mohou dostat classMap do konstruktoru
        $view->appendComponentView($this->createCompositeViewWithRenderer(HeadlineRendererEditable::class), self::HEADLINE);
        $view->appendComponentView($this->createCompositeViewWithRenderer(PerexRendererEditable::class), self::PEREX);
        $view->appendComponentView($this->createCompositeViewWithRenderer(ContentsRendererEditable::class), self::PARTS);
    }

    private function addChildComponents(CompositeViewInterface $view) {
        // renderery musí být definovány v Renderer kontejneru - tam mohou dostat classMap do konstruktoru
        $view->appendComponentView($this->createCompositeViewWithRenderer(HeadlineRenderer::class), self::HEADLINE);
        $view->appendComponentView($this->createCompositeViewWithRenderer(PerexRenderer::class), self::PEREX);
        $view->appendComponentView($this->createCompositeViewWithRenderer(ContentsRenderer::class), self::PARTS);
    }

    /**
     * Informuje. jestli paper má zobrazitelný obsah. Za paper se zobrazitelným obsahem považuje takový, který má alespoň neprázdný titulek nebo nebo neprázdný perex nebo nastavenou šablonu.
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
