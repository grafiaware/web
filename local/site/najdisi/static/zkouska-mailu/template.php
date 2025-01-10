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
echo Html::p(Html::tag('a', 
                    [
                        'href'=>"auth/v1/testmail",
                    ],
        "testmail Ťuk!"
                ));
