<?php
/**
 * A6 (docs/vp/zadani/A6.md): blok „Nový účet“ – formulář volá POST auth/v1/account.
 *
 * @var string[] $allowedRoles role, které smí přihlášený administrátor zakládat
 * @var bool $displayNameEnabled přepínač vp.osobni-jmeno (A8)
 * @var string $passwordPattern
 * @var string $passwordInfo
 */
?>
<div class="ui segment novy-ucet">
    <h3>Nový účet</h3>
    <form class="ui form" method="POST" action="auth/v1/account">
        <div class="two fields">
            <div class="required field">
                <label>E-mail</label>
                <input type="email" name="email" maxlength="100" required placeholder="jmeno@firma.cz" autocomplete="off">
                <p class="maly text">Nemusí být jedinečný – víc účtů může mít stejný e-mail.</p>
            </div>
            <div class="field">
                <label>Přihlašovací jméno</label>
                <input type="text" name="loginName" maxlength="50" pattern="[A-Za-z0-9_]{5,50}" autocomplete="off"
                       title="5 až 50 znaků: písmena bez diakritiky, číslice, podtržítko" placeholder="nevyplněno = vytvoří se z e-mailu">
                <p class="maly text">Nevyplněno = vytvoří se z e-mailu.</p>
            </div>
        </div>
        <div class="two fields">
            <?php if ($displayNameEnabled): ?>
            <div class="field">
                <label>Osobní jméno (nepovinné)</label>
                <input type="text" name="displayName" maxlength="100" placeholder="Např. Jan Novák" autocomplete="off">
            </div>
            <?php endif; ?>
            <div class="required field">
                <label>Role</label>
                <select name="role" required>
                    <option value="">vyberte roli</option>
                    <?php foreach ($allowedRoles as $role): ?>
                    <option value="<?= htmlspecialchars($role) ?>"><?= htmlspecialchars($role) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="two fields">
            <div class="field">
                <label>Heslo</label>
                <input type="password" name="password" autocomplete="new-password"
                       pattern="<?= htmlspecialchars($passwordPattern) ?>" title="<?= htmlspecialchars($passwordInfo) ?>">
                <p class="maly text"><?= htmlspecialchars($passwordInfo) ?></p>
            </div>
            <div class="field">
                <label>&nbsp;</label>
                <div class="ui checkbox">
                    <input type="checkbox" name="passwordByMail" value="1" id="passwordByMail">
                    <label for="passwordByMail">Heslo mailem – účet dostane odkaz na nastavení hesla (heslo nevyplňujte)</label>
                </div>
            </div>
        </div>
        <button class="ui primary button" type="submit">Založit</button>
    </form>
</div>
