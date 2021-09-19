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

use Component\View\Status\ButtonEditContentComponent;

use Component\View\Authored\AuthoredEnum;
use Pes\Type\ContextData;

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
     * Pokud soubor template neexistuje, použije soubor default template, pokud ani ten neexistuje, použije PaperRenderer respektive PaperEditableRenderer.
     *
     *
     */
    public function beforeRenderingHook(): void {
        if ($this->hasContent()) {

            // vytvoří komponentní view z šablony paperu nebo s ImplodeTemplate, pokud šablona paperu není nastavena
            try {
                // konstruktor PhpTemplate vyhazuje výjimku NoTemplateFileException pro neexistující (nečitený) soubor s template
                $template = new PhpTemplate($this->getTemplateFileFullname($this->configuration->getTemplatepathPaper(), $this->getTemplateName()));
            } catch (NoTemplateFileException $noTemplExc) {
                user_error("Neexistuje soubor šablony '{$this->getTemplateName()}'", E_USER_WARNING);
                $template = new ImplodeTemplate();
            }
            $templatedView = $this->createCompositeViewWithTemplate($template);
            $this->appendComponentView($templatedView, 'template');

            // zvolí PaperRenderer nebo PaperRendererEditable
            if ($this->contextData->presentEditableContent()) { // editační režim
                $paperId = $this->contextData->getPaper()->getId();
                $userPerformsActionWithContent = $this->contextData->getUserActions()->hasUserAction(AuthoredEnum::PAPER, $paperId);
                if ($userPerformsActionWithContent) {
                    $this->setRendererName(PaperRendererEditable::class);
                    // připojí k templated view komponentní view s editable renderery headline, perex, contents
                    $this->addChildEditableComponents($templatedView);
                } else {
                    $this->setRendererName(PaperRenderer::class);
                    // připojí k templated view komponentní view s editable renderery headline, perex, contents
                    $this->addChildComponents($templatedView);
                }
                // vytvoří komponent - view s buttonem ButtonEditContent
                $buttonEditContentComponent = new ButtonEditContentComponent($this->configuration);
                $this->contextData->offsetSet('typeFk', AuthoredEnum::PAPER);
                $this->contextData->offsetSet('itemId', $paperId);
                $this->contextData->offsetSet('userPerformActionWithContent', $userPerformsActionWithContent);
                $buttonEditContentComponent->setData($this->contextData);
                $buttonEditContentComponent->setRendererContainer($this->rendererContainer);
                $this->appendComponentView($buttonEditContentComponent, 'buttonEditContent');
            } else {
                $this->setRendererName(PaperRenderer::class);
                // připojí k templated view komponentní view s editable renderery headline, perex, contents
                $this->addChildComponents($templatedView);
            }
        } else {
            $this->setRendererName(EmptyContentRenderer::class);
        }
    }

    public function __toString() {
        parent::__toString();
    }

    private function addChildEditableComponents(CompositeViewInterface $view) {
        // renderery musí být definovány v Renderer kontejneru - tam mohou dostat classMap do konstruktoru
        $view->appendComponentView($this->createCompositeViewWithRenderer(HeadlineRendererEditable::class), 'headline');
        $view->appendComponentView($this->createCompositeViewWithRenderer(PerexRendererEditable::class), 'perex');
        $view->appendComponentView($this->createCompositeViewWithRenderer(ContentsRendererEditable::class), 'contents');
    }

    private function addChildComponents(CompositeViewInterface $view) {
        // renderery musí být definovány v Renderer kontejneru - tam mohou dostat classMap do konstruktoru
        $view->appendComponentView($this->createCompositeViewWithRenderer(HeadlineRenderer::class), 'headline');
        $view->appendComponentView($this->createCompositeViewWithRenderer(PerexRenderer::class), 'perex');
        $view->appendComponentView($this->createCompositeViewWithRenderer(ContentsRenderer::class), 'contents');
    }

    private function getTemplateName() {
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
    private function hasContent(): bool {
        $paper = $this->contextData->getPaper();
        return isset($paper) ? true : false;
    }
}
