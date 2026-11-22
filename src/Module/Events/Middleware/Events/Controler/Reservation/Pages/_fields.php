<?php
/**
 * R1 (#48): pole formuláře rezervace – veřejný formulář na webu (statická šablona vp/rezervace-skol), správa odkazem,
 * administrace. Obal formuláře (<form>, tlačítka) dodá volající. Prvky .field fungují v Semantic UI i na stránkách Pages/.
 *
 * @var string $mode 'public' | 'owner' | 'admin'
 * @var array $values aktuální hodnoty (sloupce school_reservation, event_id_fk)
 * @var array $lectures přednášky nabídky (SchoolReservationService::lectures())
 * @var \Events\Service\Reservation\ReservationConfig $config
 * @var string $idPrefix prefix id prvků (víc formulářů na stránce)
 */
use Events\Service\Reservation\ReservationValidator;
use Events\Service\Reservation\SchoolReservationService;

$h = fn($v) => htmlspecialchars((string) $v);
$p = $idPrefix ?? 'r';
$val = fn(string $k) => $h($values[$k] ?? '');
$currentEvent = (int) ($values['event_id_fk'] ?? 0);
$ownPersons = isset($values['id']) && ($values['status'] ?? '') === SchoolReservationService::ACTIVE
    ? (int) $values['pupils'] + (int) $values['teachers'] : 0;
$time = fn($l) => SchoolReservationService::fmt($l['start'], 'G:i') . '–' . SchoolReservationService::fmt($l['end'], 'G:i');
?>
<div class="field">
    <label for="<?= $p ?>-event">Přednáška <span class="req">*</span></label>
    <select id="<?= $p ?>-event" name="event_id" required>
        <option value="">– vyberte přednášku –</option>
        <?php foreach ($lectures as $l):
            $isCurrent = (int) $l['id'] === $currentEvent;
            $free = $l['free'] === null ? null : $l['free'] + ($isCurrent ? $ownPersons : 0);
            $available = $mode === 'admin' || ($l['bookable'] && $free !== null && $free > 0) || $isCurrent;
            $label = $time($l) . ' ' . ($l['title'] ?? '(bez názvu)');
            if ($mode === 'admin') {
                $label .= ' – ' . (int) $l['occupied'] . ' / ' . ($l['capacity'] ?? '∞');
            } elseif (!$l['bookable']) {
                $label .= ' – nelze rezervovat';
            } else {
                $label .= $free > 0 ? " – volno {$free} míst" : ' – obsazeno';
            }
            ?>
            <option value="<?= (int) $l['id'] ?>" data-free="<?= $free === null ? '' : (int) $free ?>" <?= $isCurrent ? 'selected' : '' ?>
                <?= $available ? '' : 'disabled' ?>><?= $h($label) ?></option>
        <?php endforeach; ?>
    </select>
    <?php if ($mode !== 'admin'): ?>
        <p class="muted" style="margin:4px 0 0">Kapacita zahrnuje žáky i pedagogy. Když je přednáška plná, vyberte jinou.</p>
    <?php endif; ?>
</div>
<div class="grid2 two fields">
    <div class="field">
        <label for="<?= $p ?>-school">Název školy <span class="req">*</span></label>
        <input type="text" id="<?= $p ?>-school" name="school_name" required maxlength="200" value="<?= $val('school_name') ?>"
               autocomplete="organization">
    </div>
    <div class="field">
        <label for="<?= $p ?>-place">Obec <span class="req">*</span></label>
        <input type="text" id="<?= $p ?>-place" name="school_place" required maxlength="100" value="<?= $val('school_place') ?>"
               autocomplete="address-level2">
    </div>
</div>
<div class="grid2 two fields">
    <div class="field">
        <label for="<?= $p ?>-field">Cílový obor <span class="req">*</span></label>
        <select id="<?= $p ?>-field" name="field_code" required>
            <option value="">– vyberte –</option>
            <?php foreach (ReservationValidator::FIELDS as $code => $name): ?>
                <option value="<?= $h($code) ?>" <?= ($values['field_code'] ?? '') === $code ? 'selected' : '' ?>><?= $h($name) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="field">
        <label for="<?= $p ?>-class">Třída</label>
        <input type="text" id="<?= $p ?>-class" name="school_class" maxlength="50" placeholder="např. 9. A" value="<?= $val('school_class') ?>">
    </div>
</div>
<div class="grid2 two fields">
    <div class="field">
        <label for="<?= $p ?>-pupils">Počet žáků <span class="req">*</span></label>
        <input type="number" id="<?= $p ?>-pupils" name="pupils" required min="1" max="<?= (int) $config->maxPupils ?>" step="1"
               inputmode="numeric" value="<?= $val('pupils') ?>">
    </div>
    <div class="field">
        <label for="<?= $p ?>-teachers">Počet pedagogů</label>
        <input type="number" id="<?= $p ?>-teachers" name="teachers" min="0" max="<?= (int) $config->maxTeachers ?>" step="1"
               inputmode="numeric" placeholder="1" value="<?= $h($values['teachers'] ?? ($mode === 'public' ? '1' : '')) ?>">
    </div>
</div>
<div class="grid2 two fields">
    <div class="field">
        <label for="<?= $p ?>-email">Kontaktní e-mail <span class="req">*</span></label>
        <input type="email" id="<?= $p ?>-email" name="contact_email" required maxlength="200" value="<?= $val('contact_email') ?>"
               autocomplete="email">
    </div>
    <div class="field">
        <label for="<?= $p ?>-name">Jméno pedagoga</label>
        <input type="text" id="<?= $p ?>-name" name="contact_name" maxlength="200" value="<?= $val('contact_name') ?>" autocomplete="name">
    </div>
</div>
<?php if ($mode !== 'admin'): ?>
<div class="field">
    <label for="<?= $p ?>-phone">Mobilní telefon pedagoga</label>
    <input type="tel" id="<?= $p ?>-phone" name="contact_phone" maxlength="30" placeholder="např. 777 123 456" value="<?= $val('contact_phone') ?>"
           autocomplete="tel">
    <div class="ui checkbox check" style="margin-top:6px">
        <input type="checkbox" id="<?= $p ?>-phone-consent" name="phone_consent" value="1" <?= !empty($values['contact_phone']) ? 'checked' : '' ?>>
        <label for="<?= $p ?>-phone-consent"><?= $h($config->phoneConsentText) ?></label>
    </div>
</div>
<?php endif; ?>
<div class="field">
    <label for="<?= $p ?>-note">Poznámka</label>
    <textarea id="<?= $p ?>-note" name="note" rows="2" maxlength="2000"><?= $val('note') ?></textarea>
</div>
<?php if ($mode === 'public'): ?>
<div class="hp" aria-hidden="true" style="position:absolute; left:-10000px; width:1px; height:1px; overflow:hidden">
    <label for="<?= $p ?>-hp">Nevyplňujte</label>
    <input type="text" id="<?= $p ?>-hp" name="<?= ReservationValidator::HONEYPOT ?>" tabindex="-1" autocomplete="off">
</div>
<div class="field">
    <div class="ui checkbox check">
        <input type="checkbox" id="<?= $p ?>-consent" name="consent" value="1" required>
        <label for="<?= $p ?>-consent"><?= $h($config->consentText) ?>
            <?php if ($config->policyUrl !== ''): ?>
                <a href="<?= $h($config->policyUrl) ?>" target="_blank" rel="noopener">Zásady zpracování osobních údajů</a>
            <?php endif; ?>
            <span class="req">*</span></label>
    </div>
</div>
<?php endif; ?>
