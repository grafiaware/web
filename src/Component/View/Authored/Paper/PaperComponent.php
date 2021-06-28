<?php
namespace Component\View\Authored\Paper;

use Component\View\Authored\AuthoredComponentAbstract;
use Component\ViewModel\Authored\Paper\PaperViewModelInterface;

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
        if (isset($paperAggregate)) {
            try {
                $template = $this->resolveTemplateAndShared($paperAggregate->getTemplate());
                $this->setTemplate($template);
            } catch (NoTemplateFileException $noTemplExc) {
                user_error("Neexistuje soubor šablony '{$this->getTemplatePath($paperAggregate->getTemplate())}'", E_USER_WARNING);
                $this->setTemplate(null);
            }
        }
    }
}
