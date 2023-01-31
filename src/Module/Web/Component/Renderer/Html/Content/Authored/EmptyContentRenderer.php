<?php
namespace Web\Component\Renderer\Html\Content\Authored;

use Web\Component\Renderer\Html\HtmlRendererAbstract;
use Web\Component\ViewModel\Authored\AuthoredViewModelInterface;
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
