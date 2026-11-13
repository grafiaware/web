<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
//use Red\Model\Entity\PaperAggregateInterface;
use Pes\Core\Text\Text;
use Pes\Core\Text\Html;

/** @var PhpTemplateRendererInterface $this */
 
?>   
<form class="ui huge form" action="" method="POST" > 
        <table>
        <tr>
            <td>                         
                <?= htmlspecialchars((string) $loginNameFk)  ?>
                <?php if (($displayName ?? '') !== ''): ?><br><small><?= htmlspecialchars($displayName) ?></small><?php endif; ?>
            </td>       
            <?php if (!empty($showAccountInfo)): ?>
            <td><?= htmlspecialchars((string) ($email ?? '')) ?></td>
            <?php endif; ?>
            <td>                                                       
                <div  class="field">
                    <input readonly type="text" name="passwordHash"  maxlength="255" 
                                            value="<?= isset($passwordHash) ? $passwordHash : ''?>" >
                </div>
            </td>  
            <td>                        
                <?= Html::select( "selectRole", "",  
                                  [ "selectRole" =>  $roleFk  ?? ''  ],  
                                  $selectRoles ??  [] , 
                                  [ ] ) ?>   
            </td>                
            <td>  
                <div>      
                <button class='ui secondary button' type='submit' formaction='<?= "auth/v1/credentials/$loginNameFk" ?>'> Uložit </button>
                <?php if (!empty($canSendSetPasswordLink)): ?>
                <button class='ui basic button' type='submit' formaction='<?= Text::encodeUrlPath("auth/v1/account/$loginNameFk/setpasswordlink") ?>'
                        title="Pošle na e-mail účtu nový odkaz na nastavení hesla (platí <?= (int) (\Site\ConfigurationCache::auth()['setPasswordLinkTtlHours'] ?? 72) ?> h)"> Odkaz na heslo </button>
                <?php endif; ?>
                </div>           
            </td>                   
        </tr>   
        </table>
        
        </form >
