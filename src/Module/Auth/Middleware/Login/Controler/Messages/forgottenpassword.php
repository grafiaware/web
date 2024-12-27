<?php

?>
<!DOCTYPE html>
<html lang="cs">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title></title>
    </head>
    <body  style="margin: 0; padding: 0;">
        <table role="presentation"  cellspacing="0" cellpadding="0" border="0" style="border-collapse: collapse;">
            <thead>
                <tr>                 
                    <th>
                         <img src="<?=$data_logo_grafia?>"  width="70" />                              
                         <h1>Veletrh práce a vzdělávání</h1>
                    </th>     
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <p style="margin: 0;">Děkujeme za zaslaný požadavek na vygenerování nového hesla. <br/>
                                              Vaše nové přihlašovací údaje jsou:</p>
                    </td>
                </tr>
                <tr style="align-items: center;" >
                    <td>
                        <p  style="margin: 10px 0px 10px 0px">Jméno:&nbsp;&nbsp; <b><?= $loginJmeno ?></b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Heslo:&nbsp;&nbsp; <b><?= $generatedPassword ?></b></p>
                    </td>
                </tr>    
                <tr>
                    <td>S pozdravem <br/> tým realizátora Grafia,s.r.o.</td>
                </tr>

            </tbody>
        </table>
    </body>
</html>
