<?php
namespace  Component\Renderer\Html\Menu;
use Component\View\Menu\ItemComponentInterface;
use Pes\Text\Html;

/**
 * Description of MenuWrapRenderer
 *
 * MenuWrapRenderer nahrazuje LevelWrapRendere pro nejvyšší úroveň menu - rozdíl je jen v tom, používá jinou položku class map
 *
 * @author pes2704
 */
class MenuWrapRenderer extends MenuWrapRendererAbstract {

    /**
     *
     * @var MenuViewModelInterface
     */
    protected $viewModel;

    public function render(iterable $contextData=NULL) {
        // MenuWrapRenderer nedostává zádný view model - view předá fo rendereru jen ContextData, který obsahuje vyrenderované komponentní view (tedy html)
            $levelItemsHtml = "";
            foreach ($contextData as $itemComponentHtml) {
                /** @var ItemComponentInterface $itemComponentHtml */
                $levelItemsHtml .= $itemComponentHtml;
            }

        return Html::tag('ul', ['class'=>$this->classMap->get('MenuWrap', 'ul')],$levelItemsHtml);
    }

}
