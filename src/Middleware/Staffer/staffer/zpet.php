<?php
//zpet
//echo '<form method="POST" enctype="multipart/form-data" action="?list=s07_staffer&form='.$form.'&krok=1&pozice='.$pozice.'">';
//
//nechápu smysl resetu, následného projíždění a vypisování, 
//když jde jen o návrat zpět, kde stejně nejsou žádná data... pozn.: Lukáš
/*reset ($HTTP_POST_VARS); 
while (list ($key, $val) = each ($HTTP_POST_VARS)) { 
                                                    echo '
                                                        <input type="hidden" name="'.$key.'" value="'.$val.'">'; 
                                                   }
 // ^ tohle nefungovalo, házelo chyby, nahrazeno na řádku 16 a níž :) - pozn.: Lukáš
 // tenhle kus kódu je tu vlastně taky k ničemu, když je všechen zbytek zakomentován. pozn.: Lukáš
if (@$pole['zivotopis'][2]) {echo '<input type="hidden" name="zivotopis" value="'.$pole['zivotopis'][1].'">';}
echo '<input type="submit" value="ZPĚT" name="save">';
echo '</form>';
 */
//přidal Lukáš, aby se dalo vrátit zpět ve formuláři a dozadat údaje, na které uživatel zapomněl.
echo '<FORM>
<INPUT type="button" value="Zpátky" onClick="history.back();">
</FORM>'; 
?>
