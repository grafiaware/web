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

    protected function renderSelectTemplate(AuthoredViewModelInterface $viewModel) {
        $contentTemplateName = $viewModel->getAuthoredTemplateName();
        $authoredContentId = $viewModel->getAuthoredContentId();

        $type = $viewModel->getItemType();
        // $templateContentPostVar použito jako id pro element, na které visí tiny - POZOR - id musí být unikátní - jinak selhává tiny selektor
        switch ($type) {
            case AuthoredTypeEnum::ARTICLE:
                $templateContentPostVar = AuthoredControlerAbstract::ARTICLE_TEMPLATE_CONTENT.$authoredContentId;
                break;
            case AuthoredTypeEnum::PAPER:
                $templateContentPostVar = AuthoredControlerAbstract::PAPER_TEMPLATE_CONTENT.$authoredContentId;
                break;
            case AuthoredTypeEnum::MULTIPAGE:
                $templateContentPostVar = AuthoredControlerAbstract::MULTIPAGE_TEMPLATE_CONTENT.$authoredContentId;
                break;
            default:
                throw new UnexpectedValueException("Neznámý typ item '$type'. Použijte příkaz 'Zpět' a nepoužívejte tento typ obsahu.");
        }

        return
            // id je parametr pro togleTemplateSelect(id) - voláno onclick na button 'Vybrat šablonu stránky'
            Html::tag('div', ['id'=> $this->getTemplateSelectId($viewModel),'class'=>$this->classMap->get('Template', 'div.selectTemplate')],
                Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/$type/$authoredContentId/template"],
                    [
//                        Html::tagNopair('input', ["type"=>"hidden", "name"=>$templateNamePostVar, "value"=>$contentTemplateName]),
//
                        // class je třída pro selector v tinyInit var selectTemplateConfig
//                        Html::tag('div', ['id'=>$templateContentPostVar, 'class'=>$this->classMap->get('PaperTemplateSelect', 'div.tinySelectTemplateArticle')],''),
                        Html::tag('div', ['id'=>$templateContentPostVar, 'class'=>"tiny_select_template_$type"],''),       // POZOR - id musí být unikátní - jinak selhává tiny selektor
                    ]
                )
            );
    }

    protected function getTemplateSelectId(AuthoredViewModelInterface $viewModel) {
        $type = $viewModel->getItemType();
        $articleId = $viewModel->getAuthoredContentId(); //$article->getId();
        return "select_template_{$type}_{$articleId}";
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

    private function renderButtonsDiv(array $buttons) {
        return Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.buttons')], implode('', $buttons));
    }
}
