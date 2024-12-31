<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
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
                        <p style="color: green; margin: 10px 0px 10px 0px;">
                            Data:
                        </p>
                        <p style="color: green; margin: 10px 0px 10px 0px;">
                                <?= var_dump($requestBody).PHP_EOL; ?>
                        </p>
                        <p style="color: green; margin: 10px 0px 10px 0px;">
                                <?= $value1.PHP_EOL.$value2 ?>
                        </p>
                        <p style="margin: 10px 0px 10px 0px;">
                            Děkujeme za Vaši registraci. <br/>
                            Toto je automaticky generovaný e-mail. Na tento e-mail, prosím, neodpovídejte.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p style="margin: 10px 0px 10px 0px;">
                            <b>Svoji registraci dokončete kliknutím na níže uvedený odkaz!</b> <br/>                            
                        </p>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#dddddd" style="padding: 10px 30px 10px 30px;">
                        <table role="presentation"  cellspacing="0" cellpadding="20px" border="0" style="border-collapse: collapse;">
                            <tbody>
                                <tr>
                                    <td bgcolor="pink" style=“font-size: 14px; font-family: Arial, sans-serif; background-color: pink; padding: 0px 0px 0px 0px;">
                                        <a href="<?= $confirmationUrl ?>" target="_blank" rel="noopener"><h2>&nbsp;&nbsp;&nbsp;Potvrďte registraci!&nbsp;&nbsp;&nbsp;</h2></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p style="margin:  10px 0px 10px 0px;">Po dokončení registrace obdržíte potvrzovací e-mail. Pak se budete moci přihlašovat na web praci.najdisi.cz.</p>
                    </td>
                </tr>
                <tr>
                    <td><p style="margin:  5px 0px 5px 0px;" > S pozdravem <br/> tým realizátora Grafia,s.r.o.</p></td>
                </tr>

            </tbody>
        </table>
    </body>
</html>
