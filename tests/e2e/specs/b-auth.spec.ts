import { randomBytes } from 'node:crypto';

import { expect, test } from '@playwright/test';

import {
  appPathFromMail,
  clearMailbox,
  loginNameFromDoneMail,
  openAppPath,
  waitForMailHtml,
} from '../lib/mail';
import {
  expectLoggedIn,
  expectLoggedOut,
  logout,
  openLogin,
  openRegister,
  sendLoginLink,
  submitLogin,
  submitRegistration,
} from '../lib/pages';

/**
 * Vrstva B, první vlna: A1–A3, A5–A9 se zapnutými přepínači (E2E_AUTH_FLAGS=on)
 * a C1, C4 s vypnutými (výchozí soubor v repu).
 * Fixture přepínače nastaví před během; spec na ni nesahá.
 */

const flagsOn = process.env.E2E_AUTH_FLAGS === 'on';

test.use({ screenshot: 'off', trace: 'off', video: 'off' });
test.setTimeout(60_000);

function password(): string {
  return randomBytes(9).toString('base64url');
}

test.describe('vrstva B, přepínače vypnuté', () => {
  test.skip(flagsOn, 'Při E2E_AUTH_FLAGS=on se běží jen A1–A9.');

  test('C1 formulář bez přihlašovacího odkazu a bez pole Vaše jméno', async ({ page }) => {
    await openLogin(page);
    const login = page.locator('.modal.login');
    await expect(login).not.toContainText('Poslat přihlašovací odkaz');
    await expect(login).not.toContainText('E-mail nebo přihlašovací jméno');
    await expect(login).toContainText('Přihlašovací jméno');
    await expect(login).not.toContainText('Vaše jméno');

    await openRegister(page);
    const register = page.locator('.modal.register');
    await expect(register).not.toContainText('Vaše jméno');
    await expect(page.locator('form[action="auth/v1/loginlink"]')).toHaveCount(0);
  });

  test('C4 /auth/v1/loginlink/abc je 404', async ({ page }) => {
    const response = await page.goto('/auth/v1/loginlink/abc', { waitUntil: 'domcontentloaded' });
    expect(response, 'odpověď loginlink').not.toBeNull();
    expect(response!.status()).toBe(404);
  });
});

test.describe.serial('vrstva B, přepínače zapnuté', () => {
  test.skip(!flagsOn, 'Nejdřív fixture --flags on a E2E_AUTH_FLAGS=on.');

  const account = {
    email: `e2e.a1.${Date.now()}@example.test`,
    password: password(),
    confirmPath: '',
    loginName: '',
  };
  const withoutPassword = {
    email: `e2e.a7.${Date.now()}@example.test`,
    linkPath: '',
  };

  test('A1 registrace s heslem vloží mail do smtp4dev', async ({ page, request }) => {
    await clearMailbox(request);
    await openRegister(page);
    await submitRegistration(page, account.email, account.password);
    const html = await waitForMailHtml(request, account.email, ' - Registrace.');
    account.confirmPath = appPathFromMail(html, '/auth/v1/confirm/');
  });

  test('A2 otevření odkazu registraci nedokončí', async ({ page }) => {
    await openAppPath(page, account.confirmPath);
    await expect(page.getByRole('button', { name: 'Dokončit registraci' })).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Dokončení registrace' })).toBeVisible();
    await expectLoggedOut(page);
  });

  test('A3 tlačítko dokončí registraci a přihlásí', async ({ page, request }) => {
    await openAppPath(page, account.confirmPath);
    await page.getByRole('button', { name: 'Dokončit registraci' }).click();
    await expectLoggedIn(page);
    await expect(page.locator('[data-info="flash"]')).toContainText('jste přihlášeni');
    const html = await waitForMailHtml(request, account.email, 'Registrace dokončena.');
    account.loginName = loginNameFromDoneMail(html);
  });

  test('A5 přihlášení e-mailem a heslem', async ({ page }) => {
    await openLogin(page);
    await submitLogin(page, account.email, account.password);
    await expectLoggedIn(page);
    await expect(page.locator('[data-info="flash"]')).toContainText('Jste přihlášeni.');
  });

  test('A6 přihlášení jménem a heslem', async ({ page }) => {
    await openLogin(page);
    await submitLogin(page, account.loginName, account.password);
    await expectLoggedIn(page);
    await expect(page.locator('[data-info="flash"]')).toContainText('Jste přihlášeni.');
  });

  test('A7 registrace bez hesla, heslem se pak přihlásit nejde', async ({ page, request }) => {
    await clearMailbox(request);
    await openRegister(page);
    await submitRegistration(page, withoutPassword.email);
    const html = await waitForMailHtml(request, withoutPassword.email, ' - Registrace.');
    const path = appPathFromMail(html, '/auth/v1/confirm/');
    await openAppPath(page, path);
    await expectLoggedOut(page);
    await page.getByRole('button', { name: 'Dokončit registraci' }).click();
    await expectLoggedIn(page);
    await logout(page);
    await openLogin(page);
    await submitLogin(page, withoutPassword.email, password());
    await expect(page.locator('[data-info="flash"]')).toContainText('Neplatné přihlášení');
    await expectLoggedOut(page);
  });

  test('A8 přihlašovací odkaz otevře stránku s tlačítkem a přihlásí', async ({ page, request }) => {
    await clearMailbox(request);
    await openLogin(page);
    await sendLoginLink(page, withoutPassword.email);
    const html = await waitForMailHtml(request, withoutPassword.email, 'Přihlašovací odkaz.');
    withoutPassword.linkPath = appPathFromMail(html, '/auth/v1/loginlink/');
    await openAppPath(page, withoutPassword.linkPath);
    await expect(page.getByRole('button', { name: 'Přihlásit se' })).toBeVisible();
    await expect(page.getByText('Odkaz lze použít jen jednou.')).toBeVisible();
    await expectLoggedOut(page);
    await page.getByRole('button', { name: 'Přihlásit se' }).click();
    await expectLoggedIn(page);
    await expect(page.locator('[data-info="flash"]')).toContainText('Jste přihlášeni.');
  });

  test('A9 stejný přihlašovací odkaz podruhé neplatí', async ({ page }) => {
    await openAppPath(page, withoutPassword.linkPath);
    await expect(page.getByRole('heading', { name: 'Odkaz už neplatí' })).toBeVisible();
  });
});
