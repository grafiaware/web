<?php
use Pes\Text\Text;
use Pes\Text\Html;
use Pes\View\Renderer\PhpTemplateRendererInterface;
echo Html::p(Html::tag('a', 
                    [
                        'href'=>"sendmail/v1/sendmailVS",
                    ],
        "sendmailVS Ťuk!"
                ));
echo Html::p(Html::tag('form', 
                    [
                        'method'=>'POST',
                        'action'=>"auth/v1/testmail",
                    ],
                    Html::tag('button', ['type'=>'submit'],
                    "testmail Ťuk!")
                ));

echo "-------------------------------------------";
echo Html::p(Html::tag('form', 
                    [
                        'method'=>'POST',
                        'action'=>"events/v1/sendjobrequestnewzkouska/visitor/6", 
                    ],
                    Html::tag('button', ['type'=>'submit'],
                    "jobRequestMail")
                ));
