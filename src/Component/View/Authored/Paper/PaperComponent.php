<?php
namespace Component\View\Authored\Paper;

use Component\View\Authored\Paper\AuthoredComponentAbstract;

use Component\ViewModel\Authored\Paper\PaperViewModelInterface;

/**
 * Description of NamedItemComponent
 *
 * @author pes2704
 */
class PaperComponent extends AuthoredComponentAbstract implements PaperComponentInterface {

    /**
     *
     * @var PaperViewModelInterface
     */
    protected $viewModel;


    public function __construct(PaperViewModelInterface $viewModel) {
        $this->viewModel = $viewModel;
    }

    public function setItemId($menuItemId): PaperComponentInterface {
        $this->viewModel->setItemId($menuItemId);
        return $this;
    }
}
