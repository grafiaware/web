<?php
namespace Component\Renderer\Html\Authored;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Authored\AuthoredViewModelInterface;

use Red\Model\Enum\AuthoredTypeEnum;
use Red\Middleware\Redactor\Controler\AuthoredControlerAbstract;
use Pes\Text\Html;

/**
 * Description of AuthoredRendererAbstract
 *
 * @author pes2704
 */
abstract class AuthoredRendererAbstract extends HtmlRendererAbstract {

##### společné - authored

    protected function renderRibbon(AuthoredViewModelInterface $viewModel) {
        $menuItem = $viewModel->getMenuItem();
        $type = $viewModel->getItemType();  // spoléhám na to, že návratová hodnota je hodnota z AuthoredTypeEnum

        //TODO: barvy do css - KŠ
        $class = $this->classMap->get('Buttons', 'div.ribbon-article');

        return
            Html::tag('div', ['class'=>$class], //lepítko s buttony
                Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.semafor')], //aktivní/neaktivní paper
                    Html::tag('div', ['class'=> 'ikona-popis', 'data-tooltip'=> $menuItem->getActive() ? "published" : "not published"],
                        Html::tag('i', ['class'=> $this->classMap->resolve($menuItem->getActive(), 'Icons','semafor.published', 'semafor.notpublished')])
                    )
                )
                .Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.nameMenuItem')],
                    Html::tag('p', ['class'=>''],
                        $type
                        .Html::tag('span', ['class'=>''],$menuItem->getTitle())
                    )
                )
                .$this->renderArticleButtonsForm($viewModel)
            );
    }

    protected function renderArticleButtonsForm(AuthoredViewModelInterface $viewModel) {
        return Html::tag('form', ['method'=>'POST', 'action'=>""],
            Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.buttonsWrap')],
                [
                    $this->renderButtonsDiv($this->renderItemControlButtons($viewModel)),
                    $this->renderButtonsDiv($this->renderContentControlButtons($viewModel)),
                ]
            )
        );
    }

    protected function renderItemControlButtons(AuthoredViewModelInterface $viewModel): array {
        $menuItem = $viewModel->getMenuItem();
        $uid = $menuItem->getUidFk();
        $active = $menuItem->getActive();
        $type = $viewModel->getItemType();

        $buttons = [];
        $buttons[] = Html::tag('button',
                ['class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=> $active ? 'Nepublikovat' : 'Publikovat',
                'data-position'=>'top right',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/menu/$uid/toggle",
                ],
                Html::tag('i', ['class'=>$this->classMap->resolve($active, 'Icons', 'icon.notpublish', 'icon.publish')])
            );

        $buttons[] = Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=> 'Odstranit položku',
                'data-position'=>'top right',
                'formtarget'=>'_self',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/$uid/trash",
                'onclick'=>"return confirm('Jste si jisti?');"
                ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.movetotrash')])
            );
        return $buttons;
    }

    protected function getTemplateSelectId(AuthoredViewModelInterface $viewModel) {
        $type = $viewModel->getItemType();
        $articleId = $viewModel->getAuthoredContentId(); //$article->getId();
        return "select_template_{$type}_{$articleId}";
    }
    
    private function renderButtonsDiv(array $buttons) {
        return Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.buttons')], implode('', $buttons));
    }
}
