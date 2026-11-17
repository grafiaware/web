import { test, expect } from '@playwright/test';

/**
 * Příklad: po akci ve webu zkontrolovat zachycený mail ve smtp4dev.
 * Označeno test.skip — zapněte a upravte selektory podle konkrétního scénáře (A1, A2…).
 *
 * smtp4dev API (v3): GET /api/Messages
 */
test.describe.skip('Příklad: mail po akci', () => {
  const apiBase = process.env.SMTP4DEV_API_URL ?? 'http://localhost:5000';

  test('registrace pošle mail do smtp4dev', async ({ page, request }) => {
    await request.delete(`${apiBase}/api/Messages`);

    await page.goto('/auth/register'); // upravte URL
    // await page.getByLabel('E-mail').fill(`e2e-${Date.now()}@example.test`);
    // await page.getByRole('button', { name: /registrovat/i }).click();
    // await expect(page.getByText(/děkujeme/i)).toBeVisible();

    await expect.poll(async () => {
      const res = await request.get(`${apiBase}/api/Messages`);
      const data = await res.json();
      const list = Array.isArray(data) ? data : (data.results ?? data);
      return Array.isArray(list) ? list.length : 0;
    }).toBeGreaterThan(0);
  });
});
