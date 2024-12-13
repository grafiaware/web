<?php
use Pes\Text\Text;
use Pes\Text\Html;
use Pes\View\Renderer\PhpTemplateRendererInterface;

echo Html::tag('a', 
                    [
                        'href'=>"sendmail/v1/sendmailVS",
                    ],
        "Ťuk!"
                );  