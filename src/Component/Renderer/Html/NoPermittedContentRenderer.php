<?php
namespace Component\Renderer\Html;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Authored\AuthoredViewModelInterface;
use Pes\Text\Html;
/**
 * Description of EmptyRenderer
 *
 * @author pes2704
 */
class NoPermittedContentRenderer extends HtmlRendererAbstract {
    public function render(iterable $viewModel = null) {
        /** @var AuthoredViewModelInterface $viewModel */
        return Html::tag('div', ['style'=>'display: none;' ], 'No permissions for display component.');
    }
}
