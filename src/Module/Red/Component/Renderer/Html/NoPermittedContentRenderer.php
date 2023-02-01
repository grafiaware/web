<?php
namespace Red\Component\Renderer\Html;

use Red\Component\Renderer\Html\HtmlRendererAbstract;
use Red\Component\ViewModel\Authored\AuthoredViewModelInterface;
use Pes\Text\Html;
/**
 * Description of EmptyRenderer
 *
 * @author pes2704
 */
class NoPermittedContentRenderer extends HtmlRendererAbstract {
    public function render(iterable $viewModel = null) {
        /** @var AuthoredViewModelInterface $viewModel */
        if (PES_DEVELOPMENT) {
            $style = 'display: block; color: orange';
        } else {
            $style = 'display: none;';
        }
        return Html::tag('div', ['style'=> $style], 'No permissions for component.');
    }
}
