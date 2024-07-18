<?php
namespace Red\Component\Renderer\Html\Content\Authored;

use Component\Renderer\Html\HtmlRendererAbstract;
use Red\Component\ViewModel\Content\Authored\AuthoredViewModelInterface;

use Pes\Text\Html;

/**
 * Description of AuthoredRendererAbstract
 *
 * @author pes2704
 */
abstract class AuthoredRendererAbstract extends HtmlRendererAbstract {

##### společné - authored

    protected function renderSelectTemplate(AuthoredViewModelInterface $viewModel) {

        //TODO: default hodnota šablony - současná hodnota
        $contentTemplateName = $viewModel->getAuthoredTemplateName();
        $contentType = $viewModel->getAuthoredContentType();
        $contentId = $viewModel->getAuthoredContentId();

        return
            // id je parametr pro toggleTemplateSelect(id) - voláno onclick na button 'Vybrat šablonu stránky'
            Html::tag('div', ['id'=>"select_template_paper_$contentId",'class'=>$this->classMap->get('Template', 'div.selectTemplate')],
                Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/$contentType/$contentId/template"],
                    [
//                        Html::tagNopair('input', ["type"=>"hidden", "name"=>"template_$paperId", "value"=>$contentTemplateName]),
                        // id?
                        // class je třída pro selector v tinyInit var selectTemplateConfig
                        Html::tag('div', ['id'=>"{$contentType}_{$contentId}", 'class'=>"tiny_select_template_{$contentType}"],''),
                    ]
                )
            );
    }

    protected function renderRibbon(AuthoredViewModelInterface $viewModel) {
        $menuItem = $viewModel->getMenuItem();
        $type = $viewModel->getAuthoredContentType();  // spoléhám na to, že návratová hodnota je hodnota z AuthoredTypeEnum

        //TODO: barvy do css - KŠ
        $class = $this->classMap->get('Buttons', 'div.ribbon-article');

        return
            Html::tag('div', ['class'=>$class], //lepítko s buttony
                Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.semafor')], //aktivní/neaktivní paper
                    Html::tag('div', ['class'=> 'ikona-popis', 'data-tooltip'=> $menuItem->getActive() ? "published" : "not published", 'data-position'=>'bottom center'],
                        Html::tag('i', ['class'=> $this->classMap->resolve($menuItem->getActive(), 'Icons','semafor.published', 'semafor.notpublished')])
                    )
                )
                .Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.nameMenuItem')],
                    Html::tag('p', ['class'=>''],
                        $type
                        ."&nbsp"
                        .Html::tag('span', ['class'=>''],$menuItem->getTitle())
                    )
                )
                .$this->renderArticleButtonsForm($viewModel)
            );
    }

    protected function renderArticleButtonsForm(AuthoredViewModelInterface $viewModel) {
        return Html::tag('form', ['class'=>'apiAction', 'method'=>'POST', 'action'=>""],
            Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.buttonsWrap')],
                [
                    $this->renderItemControlButtons($viewModel),
                    $this->renderEditControlButtons($viewModel),
                ]
            )
        );
    }

    protected function renderItemControlButtons(AuthoredViewModelInterface $viewModel): string {
        $menuItem = $viewModel->getMenuItem();
        $uid = $menuItem->getUidFk();
        $active = $menuItem->getActive();
        $type = $viewModel->getAuthoredContentType();

        $buttons = [];
        $buttons[] = Html::tag('button',
                ['class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=> $active ? 'Nepublikovat' : 'Publikovat',
                'data-position'=>'bottom center',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/menu/$uid/toggle",
                ],
                Html::tag('i', ['class'=>$this->classMap->resolve($active, 'Icons', 'icon.notpublish', 'icon.publish')])
            );

        $buttons[] = Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=> 'Odstranit položku',
                'data-position'=>'bottom center',
                'formtarget'=>'_self',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/$uid/trash",
                'onclick'=>"return confirm('Jste si jisti?');"
                ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.movetotrash')])
            );
        return $this->renderButtonsDiv($buttons);
    }

    protected function renderEditControlButtons(AuthoredViewModelInterface $viewModel): string {
        return '';
    }

    protected function getTemplateSelectId(AuthoredViewModelInterface $viewModel) {
        $type = $viewModel->getAuthoredContentType();
        $contentId = $viewModel->getAuthoredContentId();
        return "select_template_{$type}_{$contentId}";
    }

    protected function renderButtonsDiv(array $buttons) {
        return Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.buttons')], implode('', $buttons));
    }
}
