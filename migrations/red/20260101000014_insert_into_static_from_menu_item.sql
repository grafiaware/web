/*
 * Jednorázové naplnění template slugem z menu_item.title patřilo k prvnímu
 * CREATE prázdné tabulky static. Tehdy ležely šablony v jedné složce a jméno
 * složky se odvozovalo z title (malá písmena, bez diakritiky, mezery jako pomlčky).
 * Path se doplňovalo ručně až po přesunu šablon do složek.
 *
 * Baseline 2025 už má path i template vyplněné. INSERT všech static položek
 * by přidal druhé řádky bez path a rozbil vazbu menu_item → static.
 *
 * Odvození jména složky z title pro web bez tabulky static zůstává v kódu:
 * Pes\Core\Text\FriendlyUrl::friendlyUrlText(), volané z
 * Red\Service\ItemApi\ItemApiService pro api generator "template".
 */

UPDATE `static` SET `template` = `template` WHERE 0;
