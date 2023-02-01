<?php
namespace Red\Component\Renderer\Html\Content\Authored;

use Red\Component\Renderer\Html\HtmlRendererAbstract;
use Red\Component\ViewModel\Authored\AuthoredViewModelInterface;
use Pes\Text\Html;
/**
 * Description of EmptyRenderer
 *
 * @author pes2704
 */
class EmptyContentRenderer extends HtmlRendererAbstract {

    public function render(iterable $viewModel = null) {
        /** @var AuthoredViewModelInterface $viewModel */
        return Html::tag('div', ['style'=>'display: none;' ], 'No published content.');
    }
}
