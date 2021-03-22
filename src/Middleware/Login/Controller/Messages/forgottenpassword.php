<?php

?>
<!DOCTYPE html>
<html lang="cs">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>veletrhPRACE.online</title>
    </head>
    <body  style="margin: 0; padding: 0;">
        <table role="presentation"  cellspacing="0" cellpadding="0" border="0" style="border-collapse: collapse;">
            <thead>
                <tr>
                    <th><h1>Veletrh práce a vzdělávání</h1></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <p style="margin: 0;">Děkujeme za zaslaný požadavek na vygenerování nového hesla.
                            <br/><p> Vaše nové přihlašovací údaje jsou:</p></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p>Jméno:&nbsp;&nbsp; <b><?= $loginJmeno ?></b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Heslo:&nbsp;&nbsp; <b><?= $generatedPassword ?></b></p>
                    </td>
                </tr>                                       
                <tr>  
                    <td>&nbsp;</td>
                </tr>             
                <tr>
                    <td>S pozdravem <br/> tým realizátora Grafia,s.r.o.</td>
                </tr>

            </tbody>
        </table>
    </body>
</html>
