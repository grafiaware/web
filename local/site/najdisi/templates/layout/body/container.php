<?php
use Pes\Core\Text\Html;
use Pes\View\Renderer\PhpTemplateFunctionsInterface;
use Status\Session\SessionUnlockPolicy;
/** @var PhpTemplateFunctionsInterface $this */
?>
<style>
    /*Loader načítání stránky*/
    #loader{
        position: absolute;
        width: 120px;
        height: 120px;
        top: 50%;
        left: 50%; 
        transform: translate(-50%, -50%);
        &::after{
            content: '';
            position: absolute;
            border: 12px solid #f3f3f3;
            border-radius: 50%;
            border-top: 12px solid darkred;
            width: 100%;
            height: 100%;
            box-sizing: border-box;
            animation: spin 1s linear infinite;
        }
    }
    .spinnertext{
        color: black;
        position: absolute;   
        top: 50%;
        left: 50%; 
        transform: translate(-50%, -50%);
    }
    @keyframes spin {
        100% {
            transform: rotate(360deg);
        }
    }
</style>

    <div id="loader">
        <p class="spinnertext">Načítání...</p>
    </div>
    <?php if (\Auth\Service\GlobalRoleGrantor::fromConfiguration()->isActive()): /* A9: docs/vp/zadani/A9.md */ ?>
    <div class="global-role-grantor-varovani" role="alert"
         style="position: sticky; top: 0; z-index: 2000; background: #9f1c1c; color: #fff; padding: 10px 16px; font-weight: bold; text-align: center;">
        Web se nesmí používat: není vypnuté globální přidělování rolí (global_role_grantor).
        <span style="font-weight: normal;">Po dokončení zásahu ho správce serveru vypne v konfiguraci (ConfigurationRoot.local.php).</span>
    </div>
    <?php endif; ?>
    <div id="loaded">
        <div class="ui grid centered">
            <div class="row">
                <?= $this->insert( __DIR__.'/container/telo.php', $context); ?>
                <?= Html::tag('div', ['class'=>'cascade', 'data-red-apiuri'=>SessionUnlockPolicy::URI_FLASH,])  ?>
                <?= Html::tag('div', ['class'=>'cascade', 'data-red-apiuri'=>"web/v1/component/infoBoard",])  ?>
            </div>
            <div class="row">
                <div class="thirteen wide computer twelve wide large screen twelve wide widescreen column">
                     <footer id="footer">
                        <span id="kontakty"></span>
                        <?php include "container/paticka.php"; ?>
                    </footer>
                </div>
            </div>
        </div>
    </div>

