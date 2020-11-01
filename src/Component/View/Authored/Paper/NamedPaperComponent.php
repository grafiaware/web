<?php
namespace Component\View\Authored\Paper;

use Component\View\Authored\AuthoredComponentAbstract;

use Component\ViewModel\Authored\Paper\NamedPaperViewModelInterface;

/**
 * Description of NamedItemComponent
 *
 * @author pes2704
 */
class NamedPaperComponent extends AuthoredComponentAbstract implements NamedComponentInterface {

    /**
     * @var NamedPaperViewModelInterface
     */
    protected $viewModel;

    public function __construct(NamedPaperViewModelInterface $viewModel) {
        $this->viewModel = $viewModel;
    }

    public function setComponentName($componentName): NamedComponentInterface {
        $this->viewModel->setComponentName($componentName);
        return $this;
    }
}
