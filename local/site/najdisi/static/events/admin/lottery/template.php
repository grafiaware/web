<?php
/**
 * D1 (#46): vstup do administrace losování ze statické stránky (položka menu).
 * Stránky losování (přehled, nastavení, kola, ceny, anketa, papírové lístky, skener, losování kola) jsou samostatné
 * stránky bez layoutu webu (Pages/ v Events, ADR 002); odtud se na ně přechází odkazem (GET), akce na nich mají vlastní formuláře.
 */
use Pes\Core\Text\Html;
use Pes\View\Renderer\PhpTemplateRendererInterface;

use Application\Feature\FeatureFlags;
use Component\ViewModel\StatusViewModel;
use Component\ViewModel\StatusViewModelInterface;
use Access\Enum\RoleEnum;

/** @var PhpTemplateRendererInterface $this */

if (!FeatureFlags::fromConfiguration()->isEnabled('vp.losovani')) {
    echo Html::p("Losování není zapnuté.", ["class" => "ui segment"]);
    return;
}

/** @var StatusViewModelInterface $statusViewModel */
$statusViewModel = $container->get(StatusViewModel::class);
if ($statusViewModel->getUserRole() == RoleEnum::EVENTS_ADMINISTRATOR) {
?>
    <div class="ui segment">
        <p>Nastavení losování, kola a ceny, anketa, papírové lístky, skener u vchodu a losování kol.</p>
        <a class="ui primary button" href="events/v1/lottery">Administrace losování</a>
    </div>
<?php
} else {
    echo Html::p("Stránka je určena pouze pro administraci.", ["class" => "ui orange segment"]);
}
