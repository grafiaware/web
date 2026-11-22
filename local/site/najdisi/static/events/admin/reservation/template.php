<?php
/** R1 (#48): vstup do administrace rezervací pro školy (jen events_administrator). */
use Application\Feature\FeatureFlags;
use Component\ViewModel\StatusViewModel;
use Access\Enum\RoleEnum;

if (!FeatureFlags::fromConfiguration()->isEnabled('vp.rezervace')) {
    echo '<p class="ui segment">Rezervace nejsou zapnuté.</p>';
    return;
}
if ($container->get(StatusViewModel::class)->getUserRole() != RoleEnum::EVENTS_ADMINISTRATOR) {
    echo '<p class="ui orange segment">Stránka je určena pouze pro administraci programu.</p>';
    return;
}
?>
<div class="ui segment">
    <h3>Rezervace přednášek pro školy</h3>
    <p>Nabídka, výběr přednášek, přehled rezervací s telefony pedagogů, export a mazání osobních údajů.
        Kapacitu přednášek nastavíte ve správě eventů.</p>
    <p><a class="ui primary button" href="events/v1/reservation">Otevřít administraci rezervací</a></p>
</div>
