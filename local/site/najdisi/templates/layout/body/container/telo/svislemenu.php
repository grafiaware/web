<?php
use Pes\Text\Html;
?>
        <!-- #mySidenav s třídou .open se menu neskryje při kliknutí mimo oblast menu-->
        <!-- #mySidenav s třídou .editMenu vznikne nescrollovatelné svislé menu; k rodiči tohoto elementu - <div class="fix-bar"> se ještě musí přidat class .no-fix (než bude podpora :has())-->
        <div id="mySidenav" class="editMenu"> 
            <div class="close-item" onclick="hamburger_close()"><a href="javascript:void(0)"><i class="times circle outline large icon"></i>Zavřít</a></div>
  
            <nav class="svisle-menu">
                <?= Html::tag('div', ['class'=>'cascade', 'data-red-apiuri'=>"red/v1/component/menuSupervisor",])  ?>
            </nav>
            <nav class="svisle-menu">
                <?= Html::tag('div', ['class'=>'cascade', 'data-red-apiuri'=>"red/v1/component/menuEventsAdmin",])  ?>
            </nav>                     
            <nav class="svisle-menu">
                <?= Html::tag('div', ['class'=>'cascade', 'data-red-apiuri'=>"red/v1/component/menuEventsRepresentative",])  ?>
            </nav>            
            <nav class="svisle-menu">
                <?= Html::tag('div', ['class'=>'cascade', 'data-red-apiuri'=>"red/v1/component/menuEventsVisitor",])  ?>
            </nav>            
            <nav class="svisle-menu">
                <?= Html::tag('div', ['class'=>'cascade', 'data-red-apiuri'=>"red/v1/component/menuVertical",])  ?>
            </nav>
            <nav class="svisle-menu kos">
                <?= Html::tag('div', ['class'=>'cascade', 'data-red-apiuri'=>"red/v1/component/menuTrash",])  ?>
            </nav>
            </nav>
            <nav class="svisle-menu bloky">
                <?= Html::tag('div', ['class'=>'cascade', 'data-red-apiuri'=>"red/v1/component/menuBlocks",])  ?>
            </nav>
        </div>
        <div id="myOverlay" onclick="hamburger_close()"></div>
        <!--<div class="nav-mobile active" onclick="hamburger_open()"><div><i class="bars big icon"></i><p>Menu</p></div></div>-->