<?php
namespace Component\View\Authored\Paper;

use Pes\View\Renderer\RendererInterface;
use Pes\View\Renderer\RendererModelAwareInterface;
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

    /**
     * Přetěžuje view model Pes View, upřesňuje typ view modelu.
     * @var PaperViewModelInterface
     */
    protected $viewModel;

    /**
     * Přetěžuje konstruktor CompositeComponentAbstract, upřesňuje typ parametru (view modelu).
     * @param PaperViewModelInterface $viewModel
     */
    public function __construct(PaperViewModelInterface $viewModel) {
        $this->viewModel = $viewModel;
    }

    public function setItemId($menuItemId): PaperComponentInterface {
        $this->viewModel->setItemId($menuItemId);
        return $this;
    }


    /**
     * Přetěžuje metodu View->beforeRenderingHook().
     * Pokud entita PaperAggregate má nastaven název template, metoda se pokusí nalézt soubor s šablonou a vytvořit PhpTemplate objekt. Pokud uspěje
     * nastaví PhpTemplate objekt jako template pro component (view).
     * Následně metoda rodičovského view resolveRenderer() automaticky zvolí jako renderer PHP template renderer.
     */
    protected function beforeRenderingHook(): void {
        $paperAggregate = $this->viewModel->getPaper();
        if (isset($paperAggregate)) {
            $templateName = $paperAggregate->getTemplate();
            if (isset($templateName) AND $templateName) {
                $templatePath = $this->getPaperTemplatePath($templateName);
                try {
                    $this->setTemplate(new PhpTemplate($templatePath));  // exception
                } catch (NoTemplateFileException $noTemplExc) {
                    user_error("Neexistuje soubor šablony $templatePath", E_USER_WARNING);
                }
            }
        }
        return ;
    }
}
