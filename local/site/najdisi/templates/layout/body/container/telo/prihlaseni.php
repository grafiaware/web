<?php
use Pes\Text\Html;
?>

<div class="prihlaseni">
            <?=
                Html::tag('div', ['class'=>'cascade', 'data-red-apiuri'=>"auth/v1/component/login",]) 
                . 
                Html::tag('div', ['class'=>'cascade', 'data-red-apiuri'=>"auth/v1/component/logout",]) 
                . 
                Html::tag('div', ['class'=>'cascade', 'data-red-apiuri'=>"auth/v1/component/register",]) 
                . 
                Html::tag('div', ['class'=>'cascade', 'data-red-apiuri'=>"red/v1/component/presentationAction",]) 
                .
                Html::tag('div', ['class'=>'cascade', 'data-red-apiuri'=>"events/v1/component/representativeAction",]) ;
            ?>
        </div>