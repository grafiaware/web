<form action="index.php" method="GET">
<input  type="hidden"  name="list" value="hledani2">
<input  type="hidden"  name="lang" value="<?php echo $langCode;?>">
<div class="ui mini action fluid input">
<?php
switch ($langCode) {
    case 'en':
        $placeholder="Search...";
        break;
    case 'de':
        $placeholder="Suchen...";
        break;
    case 'cs':
    default:
        $placeholder="Vyhledat...";
        break;
}

 echo '<input placeholder="'.$placeholder.'" type="text" name="klic" value=""  minlength="4" maxlength="200">';
?>
    <button class="ui icon button" type="submit"><i class="search link icon"></i></button>
</div>
</form>



