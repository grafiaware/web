<?php ; ?>
<fieldset>
    <legend>
            Import souboru
    </legend>
    <form method="POST" enctype="multipart/form-data" action="index.php?list=stranky&stranka=<?php echo $stranka;?>&language=<?php echo $lang;?>">
        Název souboru:<br>
        
        <input type="text" size="60" name="finazev"><br>
        Umístění souboru na Vašem lokálním počítači:<br> 
        <input type="file" name="fiadresa" size="60"><br>
        <input type="radio" name="link_type" value="view" checked> Zobrazit soubor v prohlížeči po kliknutí na odkaz <i>např. pdf</i>)<br>
        <input type="radio" name="link_type" value="picture"> Zobrazit soubor jako obrázek <i>pouze pro formáty souborů jpg, gif a png</i>)<br>
        <input type="radio" name="link_type" value="download"> Nabídnout soubor ke stažení<br>
        <br><br><input type="submit" value="importovat" name="newfile2">
        <input type="hidden" name="stranka" value="<?php echo $stranka;?>">
                                  
    </form>
</fieldset>

<?php
