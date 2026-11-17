-- G0 (#116): stará produkční data programu (online veletrh 2021–22) se nepoužijí – rozhodl Petr 17. 11. 2026.
-- Smaže přihlášky, eventy a obsahy eventů a vynuluje čítače (enroll čítač nemá – klíč login + event).
-- Struktura tabulek se nemění.
-- Typy obsahu, instituce, typy institucí, odkazy a fáze odkazů zůstávají.
-- NEVRATNÉ: před spuštěním na produkci zálohovat DB (standardní postup /build).
-- Pořadí kvůli cizím klíčům: enroll -> event -> event_content.

DELETE FROM enroll;
DELETE FROM event;
DELETE FROM event_content;

ALTER TABLE event AUTO_INCREMENT = 1;
ALTER TABLE event_content AUTO_INCREMENT = 1;
