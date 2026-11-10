<?php
/**
 * A9 (docs/vp/zadani/A9.md): blok „Nový účet“ – jen účet typu admin. Formulář volá POST auth/v1/account.
 * Heslo nezadává správce: účet vždy dostane e-mailem odkaz na nastavení hesla.
 *
 * @var string[] $allowedRoles role, které smí přihlášený přidělit
 * @var bool $displayNameEnabled přepínač vp.osobni-jmeno (A8)
 * @var int $ttlHours platnost odkazu na heslo
 */
?>
<div class="ui segment novy-ucet">
    <h3>Nový účet</h3>
    <p class="maly text">Účet založený v administraci: identitou je přihlašovací jméno (nejde změnit), e-mail slouží jen ke komunikaci.
       Na e-mail přijde odkaz na nastavení hesla (platí <?= (int) $ttlHours ?> h).</p>
    <form class="ui form" method="POST" action="auth/v1/account">
        <div class="two fields">
            <div class="required field">
                <label>Přihlašovací jméno</label>
                <input type="text" name="loginName" maxlength="50" required pattern="[A-Za-z0-9_]{5,50}" autocomplete="off"
                       title="Jedno slovo: 5 až 50 znaků, písmena bez diakritiky, číslice, podtržítko" placeholder="např. novak_jan">
            </div>
            <div class="required field">
                <label>E-mail</label>
                <input type="email" name="email" maxlength="100" required placeholder="jmeno@firma.cz" autocomplete="off">
                <p class="maly text">Nemusí být jedinečný – víc účtů může mít stejný e-mail.</p>
            </div>
        </div>
        <div class="two fields">
            <?php if ($displayNameEnabled): ?>
            <div class="field">
                <label>Osobní jméno (nepovinné)</label>
                <input type="text" name="displayName" maxlength="100" placeholder="Např. Jan Novák" autocomplete="off">
            </div>
            <?php endif; ?>
            <div class="field">
                <label>Role (nepovinná)</label>
                <select name="role">
                    <option value="">bez role</option>
                    <?php foreach ($allowedRoles as $role): ?>
                    <option value="<?= htmlspecialchars($role) ?>"><?= htmlspecialchars($role) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <button class="ui primary button" type="submit">Založit a poslat odkaz na heslo</button>
    </form>
</div>
