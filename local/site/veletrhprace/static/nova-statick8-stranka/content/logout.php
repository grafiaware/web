<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */
?>
    <div class="logout">
        <!-- na veletrhprace.online  :   profil návtěvníka web/v1/page/item/6062d0e00190e      ;     profil vystavovatele web/v1/page/item/6062dfc9a10ab -->
        <!--<p class="profil-ikona"><a class="link-img" href=""><i class="inverted user circle icon"></i></a></p>-->
        <form method="POST" action='auth/v1/logout'>
              <i class="phone volume alternate icon"></i>
              <div >
                <div class="item header"><p><i class="user icon"></i><?= $userName ?? 'neznám tě'?></p></div>
                <button class="ui button" type="submit" name="logout" value="1">
                    Odhlásit
                </button>
              </div>
        </form>
    </div>