<?php

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Core\Text\Html;
use Pes\Core\Text\Text;
use Access\Enum\RoleEnum;
use Component\ViewModel\StatusViewModel;
use Component\ViewModel\StatusViewModelInterface;
use Red\Model\Entity\MenuItemInterface;
use Red\Model\Entity\StaticItemInterface;
use Red\Model\Repository\MenuItemRepo;
use Red\Model\Repository\StaticItemRepo;
use Red\Service\ItemCreator\Enum\ItemApiGeneratorEnum;
use Red\Service\StaticRegistry\StaticRegistryListClientInterface;
use Site\ConfigurationCache;

/** @var PhpTemplateRendererInterface $this */
/** @var \Psr\Container\ContainerInterface $container */

/** @var StatusViewModelInterface $statusViewModel */
$statusViewModel = $container->get(StatusViewModel::class);
$userRole = $statusViewModel->getUserRole();

if ($userRole !== RoleEnum::RED_ADMINISTRATOR) {
    echo Html::p('Stránka je určena pouze pro red administraci.', ['class' => 'ui orange segment']);
    return;
}

/** @var StaticItemRepo $staticItemRepo */
$staticItemRepo = $container->get(StaticItemRepo::class);
/** @var MenuItemRepo $menuItemRepo */
$menuItemRepo = $container->get(MenuItemRepo::class);
/** @var StaticRegistryListClientInterface $registryListClient */
$registryListClient = $container->get(StaticRegistryListClientInterface::class);

$siteCode = (string) (ConfigurationCache::staticRegistry()['staticRegistry.siteCode'] ?? '');

// --- Red MySQL: static položky seskupené podle api_module_fk ---
$redByModule = [];
foreach ($staticItemRepo->findAll() as $static) {
    /** @var StaticItemInterface $static */
    $menuItem = $menuItemRepo->getById((int) $static->getMenuItemIdFk());
    if ($menuItem === null) {
        continue;
    }
    /** @var MenuItemInterface $menuItem */
    if ($menuItem->getApiGeneratorFk() !== ItemApiGeneratorEnum::STATIC_GENERATOR) {
        continue;
    }
    $module = (string) ($menuItem->getApiModuleFk() ?? '');
    if ($module === '') {
        $module = '(bez modulu)';
    }
    $redByModule[$module][] = [
        'menuItemId' => (int) $menuItem->getId(),
        'title' => (string) $menuItem->getTitle(),
        'path' => (string) ($static->getPath() ?? ''),
        'template' => (string) ($static->getTemplate() ?? ''),
        'updated' => $static->getUpdated()?->format(DATE_ATOM) ?? '',
        'active' => (bool) $menuItem->getActive(),
    ];
}
ksort($redByModule);

// --- Remote SQLite registry (auth / events) přes API ---
$remoteModules = ['events', 'auth'];
$remoteByModule = [];
foreach ($remoteModules as $module) {
    $remoteByModule[$module] = $registryListClient->fetch($module);
}

$pushSyncUrl = Text::encodeUrlPath('red/v1/static/registry/push-sync');

/**
 * @param list<array<string, mixed>> $rows
 */
$renderTable = static function (array $rows, bool $showTitle = false): void {
    if ($rows === []) {
        echo Html::p('Žádné záznamy.', ['class' => 'ui message']);
        return;
    }
    ?>
    <table class="ui celled compact table">
        <thead>
            <tr>
                <th>menuItemId</th>
                <?php if ($showTitle) { ?><th>title</th><?php } ?>
                <th>path</th>
                <th>template</th>
                <th>updated</th>
                <?php if ($showTitle) { ?><th>active</th><?php } ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $row) { ?>
            <tr>
                <td><?= htmlspecialchars((string) ($row['menuItemId'] ?? '')) ?></td>
                <?php if ($showTitle) { ?>
                    <td><?= htmlspecialchars((string) ($row['title'] ?? '')) ?></td>
                <?php } ?>
                <td><code><?= htmlspecialchars((string) ($row['path'] ?? '')) ?></code></td>
                <td><code><?= htmlspecialchars((string) ($row['template'] ?? '')) ?></code></td>
                <td><?= htmlspecialchars((string) ($row['updated'] ?? '')) ?></td>
                <?php if ($showTitle) { ?>
                    <td><?= !empty($row['active']) ? 'ano' : 'ne' ?></td>
                <?php } ?>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <?php
};

?>
<div class="ui segment">
    <h2 class="ui header">Static registry — přehled a sync</h2>
    <p>
        Site: <strong><?= htmlspecialchars($siteCode) ?></strong>.
        Red přehled je z MySQL tabulky <code>static</code>; auth/events ze SQLite přes
        <code>GET /{module}/v1/static/registry</code>.
    </p>

    <h3 class="ui dividing header">Push sync</h3>
    <form class="ui form" method="POST" action="">
        <button class="ui primary button" type="submit"
                formaction="<?= $pushSyncUrl ?>?module=events">
            Sync → events
        </button>
        <button class="ui primary button" type="submit"
                formaction="<?= $pushSyncUrl ?>?module=auth">
            Sync → auth
        </button>
    </form>
    <p class="ui small grey text">
        POST <code>/red/v1/static/registry/push-sync</code> s parametrem <code>module=events|auth</code>.
        Upsertne všechny položky z red a smaže orphan záznamy v SQLite.
        Po sync obnovte stránku, aby se propsal SQLite přehled.
    </p>
</div>

<div class="ui segment">
    <h3 class="ui dividing header">Red (MySQL) — static položky</h3>
    <?php foreach ($redByModule as $module => $rows) { ?>
        <h4 class="ui header">
            Modul <code><?= htmlspecialchars($module) ?></code>
            <span class="ui tiny label"><?= count($rows) ?></span>
        </h4>
        <?php $renderTable($rows, true); ?>
    <?php } ?>
    <?php if ($redByModule === []) { ?>
        <?= Html::p('V red DB nejsou žádné static položky.', ['class' => 'ui message']) ?>
    <?php } ?>
</div>

<?php foreach ($remoteModules as $module) {
    $remote = $remoteByModule[$module];
    $items = $remote['items'] ?? [];
    $error = $remote['error'] ?? null;
    ?>
    <div class="ui segment">
        <h3 class="ui dividing header">
            SQLite registry — <?= htmlspecialchars($module) ?>
            <span class="ui tiny label"><?= (int) ($remote['count'] ?? count($items)) ?></span>
        </h3>
        <?php if ($error) { ?>
            <div class="ui warning message"><?= htmlspecialchars((string) $error) ?></div>
        <?php } ?>
        <?php $renderTable(is_array($items) ? $items : [], false); ?>
    </div>
<?php } ?>
