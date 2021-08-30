<!--<DIV class=topmenu>-->
<!--</DIV>-->
<!--<DIV class=middmenu>-->
<!--bléé-->
<!--</DIV>-->
<!--<DIV class=menubott>-->
<!---->
<!--</DIV>-->
<DIV class=rs_topmenu>
    
</DIV>
<DIV class=rs_middmenu>
   
<?php
    if(mb_substr($list,0,13,"UTF-8") == 'katalog_kurzu') {
        echo '<a href="index.php?list=katalog_kurzu&app=edun" class=polozkaon><span id="katalog">Katalog kurzů - kurzy</span></a>';
    } else {
        echo '<a href="index.php?list=katalog_kurzu&app=edun" class=polozka><span id="katalog">Katalog kurzů - kurzy</span></a>';
    }

    
    if(mb_substr($list,0,5,"UTF-8") == 'casti') {
        echo '<a href="index.php?list=casti_kurzu&app=edun" class=polozkaon><span id="katalog">Katalog kurzů - části</span></a>';
    } else {
        echo '<a href="index.php?list=casti_kurzu&app=edun" class=polozka><span id="katalog">Katalog kurzů - části</span></a>';
    }          
    
        
    if(mb_substr($list,0,6,"UTF-8") == 'moduly') {
        echo '<a href="index.php?list=moduly_kurzu&app=edun" class=polozkaon><span id="katalog">Katalog kurzů - moduly</span></a>';
    } else {
        echo '<a href="index.php?list=moduly_kurzu&app=edun" class=polozka><span id="katalog">Katalog kurzů - moduly</span></a>';
    }    
    
    //if(mb_substr($list,0,14,"UTF-8") == 'katalog_publik') {
    //    echo '<a href="index.php?list=katalog_publikace&app=edun" class=polozkaon>Katalog kurzů - publikace</a>'; }
    //else{
    //    echo '<a href="index.php?list=katalog_publikace&app=edun" class=polozka>Katalog kurzů - publikace</a>';   }
    
    if(mb_substr($list,0,9,"UTF-8") == 'planovane') {
        echo '<a href="index.php?list=planovane_kurzy&app=edun" class=polozkaon><span id="katalog">Plánované kurzy</span></a>';
    } else {
        echo '<a href="index.php?list=planovane_kurzy&app=edun" class=polozka><span id="katalog">Plánované kurzy</span></a>';
    }
      
    
      
        
        
        
        
        
//IF (substr($list,0,7) == 'pormenu') {
//                                     echo '<a href="index.php?lang='.$lang.'&list=pormenu" class=polozkaon>Pořadí položek v menu</a>';
//                                    }
//ELSE {
//      echo '<a href="index.php?lang='.$lang.'&list=pormenu" class=polozka>Pořadí položek v menu</a>';
//     }
//IF (substr($list,0,7) == 'dellist') {
//                                     echo '<a href="index.php?lang='.$lang.'&list=dellist" class=polozkaon>Odstranit stránku</a>';
//                                    }
//ELSE {
//      echo '<a href="index.php?lang='.$lang.'&list=dellist" class=polozka>Odstranit stránku</a>';
//     }
?>
</DIV>
<DIV class=rs_menubott>
    
</DIV>
