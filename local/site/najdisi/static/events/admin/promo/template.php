<?php
/** F2 (#47): vstup do správy odkazů a přehledu zdrojů registrací (jen media_manager). */
use Application\Feature\FeatureFlags;
use Component\ViewModel\StatusViewModel;
use Access\Enum\RoleEnum;

if (!FeatureFlags::fromConfiguration()->isEnabled('vp.odkazy')) {
    echo '<p class="ui segment">Odkazy s měřením nejsou zapnuté.</p>';
    return;
}
if ($container->get(StatusViewModel::class)->getUserRole() != RoleEnum::MEDIA_MANAGER) {
    echo '<p class="ui orange segment">Stránka je určena pouze pro media managera.</p>';
    return;
}
?>
<div class="ui segment">
    <h3>Odkazy a zdroje registrací</h3>
    <p>Trvalé odkazy pro plakáty, billboardy, weby, e-maily a sociální sítě: cíl jde kdykoli změnit, návštěvy se počítají
        po kanálech a registrace podle zdroje. Podklady ke zkopírování, přehled a export.</p>
    <p><a class="ui primary button" href="events/v1/promo">Otevřít správu odkazů</a></p>
</div>
