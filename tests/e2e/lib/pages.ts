import { expect, type Page } from '@playwright/test';

export async function openRegister(page: Page): Promise<void> {
  await page.goto('/', { waitUntil: 'domcontentloaded' });
  await page.locator('button.btn-register').click();
  await expect(page.locator('.modal.register')).toBeVisible();
}

export async function openLogin(page: Page): Promise<void> {
  await page.goto('/', { waitUntil: 'domcontentloaded' });
  await page.locator('button.btn-login').click();
  await expect(page.locator('.modal.login')).toBeVisible();
}

export async function submitRegistration(page: Page, email: string, password?: string): Promise<void> {
  const modal = page.locator('.modal.register');
  await modal.locator('input[name="email"]').fill(email);
  if (password !== undefined && password !== '') {
    await modal.locator('input[type="password"]').fill(password);
  }
  await modal.locator('input[name="fieldNameExhibitor"]').setChecked(false);
  await modal.getByRole('button', { name: 'Registrovat' }).click();
  await expect(page.locator('[data-info="flash"]')).toContainText('Děkujeme. Na adresu');
  await expect(page.locator('[data-info="flash"]')).toContainText('jsme poslali e-mail');
}

export async function submitLogin(page: Page, user: string, password: string): Promise<void> {
  const form = page.locator('.modal.login form[action="auth/v1/login"]');
  await form.locator('input[type="text"]').fill(user);
  await form.locator('input[type="password"]').fill(password);
  await form.getByRole('button', { name: 'Přihlásit' }).click();
}

export async function sendLoginLink(page: Page, email: string): Promise<void> {
  const form = page.locator('.modal.login form[action="auth/v1/loginlink"]');
  await form.locator('input[type="email"]').fill(email);
  await form.getByRole('button', { name: 'Poslat přihlašovací odkaz' }).click();
  await expect(page.locator('[data-info="flash"]')).toContainText('poslali jsme na ni přihlašovací odkaz');
}

export async function logout(page: Page): Promise<void> {
  await page.locator('.logout button[name="logout"]').click({ force: true });
  await expect(page.locator('.logout')).toHaveCount(0);
  await expect(page.locator('[data-info="flash"]')).toContainText('Jste odhlášeni');
}

export async function expectLoggedIn(page: Page): Promise<void> {
  await expect(page.locator('.logout')).toHaveCount(1);
}

export async function expectLoggedOut(page: Page): Promise<void> {
  await expect(page.locator('.logout')).toHaveCount(0);
}
