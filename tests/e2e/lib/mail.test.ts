import assert from 'node:assert/strict';
import test from 'node:test';
import { appPathFromMail, loginNameFromDoneMail } from './mail-path.ts';

test('z odkazu v mailu zůstane jen cesta, host se zahodí', () => {
  const html = '<a href="https://ps.dev.local/auth/v1/confirm/abc123?x=1">Dokončit registraci</a>';
  assert.equal(appPathFromMail(html, '/auth/v1/confirm/'), '/auth/v1/confirm/abc123?x=1');
});

test('entita &amp; v href se dekóduje', () => {
  const html = '<a href="https://web/auth/v1/loginlink/tok&amp;kept=1">Přihlásit se</a>';
  assert.equal(appPathFromMail(html, '/auth/v1/loginlink/'), '/auth/v1/loginlink/tok&kept=1');
});

test('chybějící odkaz neprozradí tělo mailu', () => {
  assert.throws(() => appPathFromMail('<p>nic</p>', '/auth/v1/confirm/'), /chybí očekávaný odkaz/);
});

test('jméno z mailu o dokončení', () => {
  const html = 'Vaše přihlašovací jméno je <b>e2e_a1_jan</b> – uveďte ho';
  assert.equal(loginNameFromDoneMail(html), 'e2e_a1_jan');
});
