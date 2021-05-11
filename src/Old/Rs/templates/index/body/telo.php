<!--<div id="col1wrap" class="column">    col1wrap 
    <div id="col1pad">    col1pad 
        <div id="rs_middle">
            < ?php
                if (file_exists(\Middleware\Rs\AppContext::getScriptsDirectory()."contents/$list.php")){
                    include_once \Middleware\Rs\AppContext::getScriptsDirectory()."contents/$list.php";
                } else {
                    echo '<p class=chyba><b>Cíl je nedostupný!</b></p>';
                }
            ?>
        </div>  rs_middle 
        <hr class="separator" />
    </div> col1pad 
</div> col1wrap 

<div id="col2wrap" class="column">    /** col2wrap**/ 
    <div id="col2pad">     /** col2pad**/  
        <div id="rs_menu">
            < ?php
                include_once \Middleware\Rs\AppContext::getScriptsDirectory().'menu/menu.php';
            ?>
        </div>    menu 
        <hr class="separator" />
    </div> col2pad 
</div> col2wrap 


<div id="col3wrap" class="column">      /** col3wrap**/  
    <div id="col3pad">     /** col3pad**/ 
        <hr class="separator" />
    </div> col3pad 
</div> col3wrap -->

<div class="ui two column stackable grid equal width">
    <div class="two wide tablet three wide computer column">
        <nav class="svisle-menu">
            <?php
                include_once \Middleware\Rs\AppContext::getScriptsDirectory().'menu/menu.php';
            ?>
        </nav>
    </div>
    <div class="column">
        <?php
            if (file_exists(\Middleware\Rs\AppContext::getScriptsDirectory()."contents/$list.php")){
                include_once \Middleware\Rs\AppContext::getScriptsDirectory()."contents/$list.php";
            } else {
                echo '<p class=chyba><b>Cíl je nedostupný!</b></p>';
            }
        ?>
    </div>
</div>
