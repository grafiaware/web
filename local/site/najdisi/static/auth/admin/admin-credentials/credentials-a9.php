<?php
/**
 * A9 (docs/vp/zadani/A9.md): tabulka účtů s úpravou vedlejších údajů.
 *
 * self (registrace)   – osobní jméno, role;  admin – e-mail, osobní jméno, role;  legacy – e-mail (doplnění), osobní jméno, role.
 * Co je editovatelné, rozhoduje RoleGrantPolicy (a znovu se kontroluje na serveru).
 *
 * @var array $rows
 * @var int $linkTtlHours
 */
use Pes\Core\Text\Text;
?>
<table class="ui compact celled table ucty">
    <thead>
        <tr><th>Účet</th><th>Typ</th><th>E-mail</th><th>Osobní jméno</th><th>Role</th><th>Heslo</th><th>Poslední přihlášení</th><th></th></tr>
    </thead>
    <tbody>
    <?php foreach ($rows as $r):
        $login = (string) $r['loginNameFk'];
        $formId = 'ucet-' . substr(md5($login), 0, 12);
        $role = $r['roleFk'] === \Auth\Middleware\Login\Controler\AuthControler::NULL_VALUE ? '' : (string) $r['roleFk'];
        $editable = $r['roleEditable'] || $r['emailEditable'] || $r['displayNameEditable'];
        // nabídka rolí: role, které smí přihlášený přidělit (současná role je v ní, jinak by pole nebylo editovatelné)
        $options = $r['grantableRoles'];
    ?>
        <tr>
            <td><b><?= htmlspecialchars($login) ?></b></td>
            <td><?= htmlspecialchars($r['originLabel']) ?></td>
            <td>
                <?php if ($r['emailEditable']): ?>
                <input form="<?= $formId ?>" type="email" name="email" maxlength="100" value="<?= htmlspecialchars((string) $r['email']) ?>"
                       placeholder="<?= $r['origin'] === 'legacy' ? 'doplnit e-mail' : '' ?>">
                <?php else: ?>
                <?= htmlspecialchars((string) $r['email']) ?>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($r['displayNameEditable']): ?>
                <input form="<?= $formId ?>" type="text" name="displayName" maxlength="100" value="<?= htmlspecialchars((string) $r['displayName']) ?>">
                <?php else: ?>
                <?= htmlspecialchars((string) $r['displayName']) ?>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($r['roleEditable']): ?>
                <input form="<?= $formId ?>" type="hidden" name="changeRole" value="1">
                <select form="<?= $formId ?>" name="selectRole">
                    <option value=""<?= $role === '' ? ' selected' : '' ?>>bez role</option>
                    <?php foreach ($options as $option): ?>
                    <option value="<?= htmlspecialchars($option) ?>"<?= $option === $role ? ' selected' : '' ?>><?= htmlspecialchars($option) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php else: ?>
                <?= $role === '' ? '<i>bez role</i>' : htmlspecialchars($role) ?>
                <?php endif; ?>
            </td>
            <td><?= $r['hasPassword'] ? 'nastaveno' : '<i>není</i>' ?></td>
            <td><?= $r['lastLogin'] ? htmlspecialchars(date('j. n. Y H:i', strtotime((string) $r['lastLogin']))) : '' ?></td>
            <td>
                <form id="<?= $formId ?>" method="POST" action="<?= Text::encodeUrlPath("auth/v1/account/$login/update") ?>" style="display: inline;">
                    <?php if ($editable): ?>
                    <button class="ui secondary mini button" type="submit">Uložit</button>
                    <?php endif; ?>
                    <?php if (!empty($r['canSendSetPasswordLink']) && $r['roleEditable']): ?>
                    <button class="ui basic mini button" type="submit" formaction="<?= Text::encodeUrlPath("auth/v1/account/$login/setpasswordlink") ?>"
                            title="Pošle na e-mail účtu nový odkaz na nastavení hesla (platí <?= (int) $linkTtlHours ?> h)">Odkaz na heslo</button>
                    <?php endif; ?>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
