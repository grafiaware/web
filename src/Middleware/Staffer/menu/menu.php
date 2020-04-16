<?PHP
IF ($sess_prava['staffer']) {?>
<DIV class=rs_topmenu>
</DIV>
<DIV class=rs_middmenu>
<a href="?list=prehled_vsech_pozic&app=staffer" <?PHP IF ($list=='prehled_vsech_pozic') {echo 'class="polozkaon"';} ELSE {echo 'class="polozka"';}?>>Přehled všech pozic</a>
<a href="?list=nova_pozice&app=staffer" <?PHP IF ($list=='nova_pozice') {echo 'class="polozkaon"';} ELSE {echo 'class="polozka"';}?>>Založit novou pozici</a>
<a href="?list=prehled_kontos&app=staffer" <?PHP IF ($list=='prehled_kontos') {echo 'class="polozkaon"';} ELSE {echo 'class="polozka"';}?>>Přehled kontaktních osob</a>
<a href="?list=nova_kontos&app=staffer" <?PHP IF ($list=='nova_kontos') {echo 'class="polozkaon"';} ELSE {echo 'class="polozka"';}?>>Založit novou kontaktní osobu</a>
</DIV>
<DIV class=rs_menubott>
</DIV>
<!--<DIV class=topmenu>
</DIV>
<DIV class=middmenu>

</DIV>
<DIV class=menubott>
</DIV>-->
<?PHP ;}?>
