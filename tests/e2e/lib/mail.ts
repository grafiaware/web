import { expect, type APIRequestContext, type Page } from '@playwright/test';
import { appPathFromMail, loginNameFromDoneMail } from './mail-path';

export { appPathFromMail, loginNameFromDoneMail };

/**
 * smtp4dev jen přes HTTP API. Tělo zprávy ani odkaz se nevypisují do logu testu.
 */

export function smtpBase(): string {
  return (process.env.SMTP4DEV_API_URL ?? 'http://localhost:5000').replace(/\/$/, '');
}

type Summary = { id?: string; subject?: string; to?: string | string[] };

export async function clearMailbox(request: APIRequestContext): Promise<void> {
  const res = await request.delete(`${smtpBase()}/api/Messages`);
  if (!res.ok()) {
    throw new Error(`smtp4dev DELETE /api/Messages vrátilo stav ${res.status()}.`);
  }
}

async function listMessages(request: APIRequestContext): Promise<Summary[]> {
  const res = await request.get(`${smtpBase()}/api/Messages`);
  if (!res.ok()) {
    throw new Error(`smtp4dev GET /api/Messages vrátilo stav ${res.status()}.`);
  }
  const data = await res.json();
  const list = Array.isArray(data) ? data : (data.results ?? data.items ?? []);
  if (!Array.isArray(list)) {
    throw new Error('smtp4dev nevrátil seznam zpráv.');
  }
  return list as Summary[];
}

async function htmlOf(request: APIRequestContext, id: string): Promise<string> {
  const htmlRes = await request.get(`${smtpBase()}/api/Messages/${id}/html`);
  if (htmlRes.ok()) {
    const text = await htmlRes.text();
    const trimmed = text.trim();
    if (trimmed.startsWith('"')) {
      try {
        const parsed = JSON.parse(trimmed) as unknown;
        if (typeof parsed === 'string') {
          return parsed;
        }
      } catch {
        // tělo je HTML, ne JSON řetězec
      }
    }
    return text;
  }

  const msgRes = await request.get(`${smtpBase()}/api/Messages/${id}`);
  if (!msgRes.ok()) {
    throw new Error(`smtp4dev zpráva vrátila stav ${msgRes.status()}.`);
  }
  const msg = await msgRes.json() as { parts?: Array<{ contentType?: string; body?: string }> };
  for (const part of msg.parts ?? []) {
    const type = String(part.contentType ?? '');
    if (type.includes('html') && typeof part.body === 'string') {
      return part.body;
    }
  }
  throw new Error('smtp4dev zpráva nemá HTML tělo.');
}

function recipientMatches(to: Summary['to'], email: string): boolean {
  return JSON.stringify(to ?? '').toLowerCase().includes(email.toLowerCase());
}

/** Počká na HTML zprávy danému příjemci, jejíž předmět obsahuje subjectPart. */
export async function waitForMailHtml(
  request: APIRequestContext,
  email: string,
  subjectPart: string,
): Promise<string> {
  let html = '';
  await expect.poll(async () => {
    const list = await listMessages(request);
    for (const message of list) {
      if (!message.id) {
        continue;
      }
      const subject = String(message.subject ?? '');
      if (!subject.includes(subjectPart) || !recipientMatches(message.to, email)) {
        continue;
      }
      html = await htmlOf(request, message.id);
      return true;
    }
    return false;
  }, { timeout: 20_000, intervals: [500, 1000, 2000] }).toBeTruthy();
  return html;
}

/** Otevře cestu z mailu. Při chybě se do hlášky nedostane token z adresy. */
export async function openAppPath(page: Page, path: string): Promise<void> {
  try {
    const response = await page.goto(path, { waitUntil: 'domcontentloaded' });
    if (response === null) {
      throw new Error('bez odpovědi');
    }
  } catch {
    throw new Error('Otevření odkazu z mailu selhalo.');
  }
}
