<?php
use Pes\Core\Text\Text;
use Pes\Core\Text\Html;
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

echo Html::tag('div', 
        [
            'class'=>'cascade',
            'data-red-apiuri'=>"events/v1/component/representativeCompanyAddress",
        ]
    );

