import { test, expect } from '@playwright/test';

/**
 * Kouřový test: web odpovídá a fake SMTP (smtp4dev) API žije.
 * Plné scénáře „klikáním“ doplňujte sem / do dalších souborů ve specs/.
 */
test.describe('Docker smoke', () => {
  test('web odpoví na GET /', async ({ page }) => {
    const response = await page.goto('/', { waitUntil: 'domcontentloaded' });
    expect(response, 'odpověď z web kontejneru').not.toBeNull();
    expect(response!.status()).toBeLessThan(500);
    await expect(page.locator('body')).toBeVisible();
  });

  test('smtp4dev API je dostupné', async ({ request }) => {
    const apiBase = process.env.SMTP4DEV_API_URL ?? 'http://localhost:5000';
    const res = await request.get(`${apiBase}/api/Messages`);
    expect(res.ok(), `smtp4dev API ${apiBase}`).toBeTruthy();
    const body = await res.json();
    expect(Array.isArray(body) || typeof body === 'object').toBeTruthy();
  });
});
