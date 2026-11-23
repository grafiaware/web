/** Cesta z odkazu v mailu (host se zahodí, zůstane pathname a query). */
export function appPathFromMail(html: string, marker: string): string {
  const decoded = html.replace(/&amp;/g, '&');
  const re = new RegExp(`href=["']([^"']*${marker}[^"']*)["']`, 'i');
  const match = decoded.match(re);
  if (!match?.[1]) {
    throw new Error('V mailu chybí očekávaný odkaz.');
  }
  const raw = match[1];
  try {
    if (raw.startsWith('http://') || raw.startsWith('https://')) {
      const url = new URL(raw);
      return `${url.pathname}${url.search}`;
    }
  } catch {
    throw new Error('Odkaz v mailu nejde přečíst.');
  }
  return raw.startsWith('/') ? raw : `/${raw}`;
}

export function loginNameFromDoneMail(html: string): string {
  const match = html.match(/přihlašovací jméno je <b>([^<]+)<\/b>/);
  const name = match?.[1]?.trim() ?? '';
  if (!/^[A-Za-z0-9_]{5,50}$/.test(name)) {
    throw new Error('V mailu o dokončení chybí přihlašovací jméno.');
  }
  return name;
}
