#!/usr/bin/env python3
"""Zapne nebo vypne přepínače vrstvy B v ConfigurationFeatures.php.

Mění jen vp.registrace-emailem a vp.prihlaseni-odkazem. Ostatní klíče nechá.
"""

from __future__ import annotations

import re
import sys
from pathlib import Path

KEYS = ("vp.registrace-emailem", "vp.prihlaseni-odkazem")


def set_flags(text: str, enabled: bool) -> str:
    value = "true" if enabled else "false"
    for key in KEYS:
        pattern = re.compile(rf"('{re.escape(key)}'\s*=>\s*)(true|false)")
        text, count = pattern.subn(rf"\g<1>{value}", text, count=1)
        if count != 1:
            raise SystemExit(f"V ConfigurationFeatures.php chybí klíč {key}")
    return text


def main(argv: list[str]) -> None:
    if len(argv) != 3 or argv[2] not in ("on", "off"):
        raise SystemExit("použití: set-flags.py <soubor> on|off")
    path = Path(argv[1])
    path.write_text(set_flags(path.read_text(encoding="utf-8"), argv[2] == "on"), encoding="utf-8")


if __name__ == "__main__":
    main(sys.argv)
