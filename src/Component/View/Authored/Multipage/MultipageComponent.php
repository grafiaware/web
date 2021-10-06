<?php
namespace Component\View\Authored\Multipage;

use Site\Configuration;

use Pes\View\View;
use Pes\View\Template\PhpTemplate;
use Pes\View\Template\ImplodeTemplate;
use Pes\View\CompositeView;

use Pes\View\Template\Exception\NoTemplateFileException;

use Component\View\Authored\AuthoredComponentAbstract;
use Component\ViewModel\Authored\Multipage\MultipageViewModelInterface;

use Component\Renderer\Html\Authored\Multipage\MultipageRenderer;
use Component\Renderer\Html\Authored\Multipage\MultipageRendererEditable;

use Component\View\Manage\ButtonEditContentComponent;

use Component\View\Authored\AuthoredEnum;
use Pes\Type\ContextData;

/**
 * Description of PaperComponent
 *
 * @author pes2704
 */
class MultipageComponent extends AuthoredComponentAbstract implements MultipageComponentInterface {

    const CONTEXT_TEMPLATE = 'template';
    const CONTEXT_BUTTON_EDIT_CONTENT = 'buttonEditContent';


    /**
     *
     * @var MultipageViewModelInterface
     */
    protected $contextData;

    /**
     * Přetěžuje metodu View. Generuje PHP template z názvu template objektu Paper a použije ji.
     * Pokud soubor template neexistuje, použije soubor default template, pokud ani ten neexistuje, použije PaperRenderer respektive PaperEditableRenderer.
     *
     *
     */
    public function beforeRenderingHook(): void {
        // vytvoří komponentní view z šablony paperu nebo s ImplodeTemplate, pokud šablona paperu není nastavena
        try {
            // konstruktor PhpTemplate vyhazuje výjimku NoTemplateFileException pro neexistující (nečitený) soubor s template
            $template = new PhpTemplate($this->getTemplateFileFullname($this->configuration->getTemplatepathMultipage(), $this->getTemplateName()));
        } catch (NoTemplateFileException $noTemplExc) {
//            user_error("Neexistuje soubor šablony '{$this->getTemplateName()}'", E_USER_WARNING);
            $template = new ImplodeTemplate();
        }
        $templatedView = (new CompositeView())->setTemplate($template)->setRendererContainer($this->rendererContainer);  // "nedědí" contextData
        $subNodes = $this->contextData->getSubNodes();  //včetně kořene podstromu - tedy včetně multipage položky
        array_shift($subNodes);   //odstraní první prvek s indexem [0] a výsledné pole opět začína prvkem s indexem [0]
        foreach ($subNodes as $subNode) {
            $item = $subNode->getMenuItem();
            $templatedView->appendComponentView($this->getContentLoadScript($item), $item->getTypeFk().'_'.$item->getId());
        }
        $this->appendComponentView($templatedView, self::CONTEXT_TEMPLATE);

        // zvolí PaperRenderer nebo PaperRendererEditable
        if ($this->contextData->presentEditableContent()) { // editační režim
            $multipageId = $this->contextData->getMultipage()->getId();
            $userPerformsActionWithContent = $this->contextData->getUserActions()->hasUserAction(AuthoredEnum::MULTIPAGE, $multipageId);
            if ($userPerformsActionWithContent) {
                $this->setRendererName(MultipageRendererEditable::class);
            } else {
                $this->setRendererName(MultipageRenderer::class);
            }

            // vytvoří komponent - view s buttonem ButtonEditContent
            $buttonEditContentComponent = new ButtonEditContentComponent($this->configuration);
            $this->contextData->offsetSet(ButtonEditContentComponent::CONTEXT_TYPE_FK, AuthoredEnum::MULTIPAGE);
            $this->contextData->offsetSet(ButtonEditContentComponent::CONTEXT_ITEM_ID, $multipageId);
            $this->contextData->offsetSet(ButtonEditContentComponent::CONTEXT_USER_PERFORM_ACTION, $userPerformsActionWithContent);
            $buttonEditContentComponent->setData($this->contextData);
            $buttonEditContentComponent->setRendererContainer($this->rendererContainer);
            $this->appendComponentView($buttonEditContentComponent, self::CONTEXT_BUTTON_EDIT_CONTENT);
        } else {
            $this->setRendererName(MultipageRenderer::class);
        }
    }

    public function getString() {
        return parent::getString();
    }

    ### load scripts ###

    protected function getContentLoadScript($menuItem) {
        $menuItemType = $menuItem->getTypeFk();
        if ($menuItemType!='static') {
            $id = $menuItem->getId();
        } else {
            $id = $this->getNameForStaticPage($menuItem);
        }
        // prvek data ''loaderWrapperElementId' musí být unikátní - z jeho hodnoty se generuje id načítaného elementu - a id musí být unikátní jinak dojde k opakovanému přepsání obsahu elemntu v DOM
        $view = (new View())
                    ->setData([
                        'loaderWrapperElementId' => "content_for_item_{$id}_with_type_{$menuItemType}",
                        'apiUri' => "web/v1/$menuItemType/$id"
                        ]);
        $view->setTemplate(new PhpTemplate(Configuration::layoutController()['templates.loaderElement']));  //TODO: loader element oddělit samostatně
        $view->setRendererContainer($this->rendererContainer);
        return $view;
    }

    private function getNameForStaticPage(MenuItemInterface $menuItem) {
        $menuItemPrettyUri = $menuItem->getPrettyuri();
        if (isset($menuItemPrettyUri) AND $menuItemPrettyUri AND strpos($menuItemPrettyUri, "folded:")===0) {      // EditItemController - line 93
            $name = str_replace('/', '_', str_replace("folded:", "", $menuItemPrettyUri));  // zahodí prefix a nahradí '/' za '_' - recopročně
        } else {
            $name = $this->friendlyUrl($menuItem->getTitle());
        }
        return $name;
    }

    private function friendlyUrl($nadpis) {
        $url = $nadpis;
        $url = preg_replace('~[^\\pL0-9_]+~u', '-', $url);
        $url = trim($url, "-");
        $url = iconv("utf-8", "us-ascii//TRANSLIT", $url);
        $url = strtolower($url);
        $url = preg_replace('~[^-a-z0-9_]+~', '', $url);
        return $url;
    }

    ####

    private function getTemplateName() {
        $template = $this->contextData->getMultipage()->getTemplate();
        return (isset($template) AND $template) ? $template : self::DEFAULT_TEMPLATE_NAME;
    }

}
