// ** I18N

// Calendar EN language
// Author: Mihai Bazon, <mihai_bazon@yahoo.com>
// Encoding: any
// Distributed under the same terms as the calendar itself.

// For translators: please use UTF-8 if possible.  We strongly believe that
// Unicode is the answer to a real internationalized world.  Also please
// include your contact information in the header, as can be seen above.

// full day names
Calendar._DN = new Array
("Neděle",
 "Pondělí",
 "Úterý",
 "Středa",
 "Čtvrtek",
 "Pátek",
 "Sobota",
 "Neděle");

// Please note that the following array of short day names (and the same goes
// for short month names, _SMN) isn't absolutely necessary.  We give it here
// for exemplification on how one can customize the short day names, but if
// they are simply the first N letters of the full name you can simply say:
//
//   Calendar._SDN_len = N; // short day name length
//   Calendar._SMN_len = N; // short month name length
//
// If N = 3 then this is not needed either since we assume a value of 3 if not
// present, to be compatible with translation files that were written before
// this feature.

// short day names
Calendar._SDN = new Array
("Ne",
 "Po",
 "Út",
 "St",
 "Čt",
 "Pá",
 "So",
 "Ne");

// First day of the week. "0" means display Sunday first, "1" means display
// Monday first, etc.
Calendar._FD = 1;

// full month names
Calendar._MN = new Array
("Leden",
 "Únor",
 "Březen",
 "Duben",
 "Květen",
 "Červen",
 "Červenec",
 "Srpen",
 "Září",
 "Říjen",
 "Listopad",
 "Prosinec");

// short month names
Calendar._SMN = new Array
("Led",
 "Úno",
 "Bře",
 "Dub",
 "Kvě",
 "Čen",
 "Čec",
 "Srp",
 "Zář",
 "Říj",
 "Lis",
 "Pro");

// tooltips
Calendar._TT = {};
Calendar._TT["INFO"] = "About the calendar";

Calendar._TT["ABOUT"] =
"DHTML Date/Time Selector\n" +
"Česká verze / Author verze: Grafia s.r.o.\n" + // don't translate this this ;-)
"" +
"" +
"\n\n";

Calendar._TT["PREV_YEAR"] = "Předchozí rok (přidržte pro zobrazení menu)";
Calendar._TT["PREV_MONTH"] = "Předchozí měsíc (přidržte pro zobrazení menu)";
Calendar._TT["GO_TODAY"] = "Jdi na dnešní den";
Calendar._TT["NEXT_MONTH"] = "Další měsíc (přidržte pro zobrazení menu)";
Calendar._TT["NEXT_YEAR"] = "Další rok (přidržte pro zobrazení menu)";
Calendar._TT["SEL_DATE"] = "Vyberte datum";
Calendar._TT["DRAG_TO_MOVE"] = "Drag to move";
Calendar._TT["PART_TODAY"] = " (dnešní den)";

// the following is to inform that "%s" is to be the first day of week
// %s will be replaced with the day name.
Calendar._TT["DAY_FIRST"] = "Zobrazit %s první";

// This may be locale-dependent.  It specifies the week-end days, as an array
// of comma-separated numbers.  The numbers are from 0 to 6: 0 means Sunday, 1
// means Monday, etc.
Calendar._TT["WEEKEND"] = "0,6";

Calendar._TT["CLOSE"] = "Zavřít";
Calendar._TT["TODAY"] = "Dnešní den";
Calendar._TT["TIME_PART"] = "(Shift-)Klikněte nebo táhněte pro změnu hodnoty";

// date formats
Calendar._TT["DEF_DATE_FORMAT"] = "%Y-%m-%d";
Calendar._TT["TT_DATE_FORMAT"] = "%a, %b %e";

Calendar._TT["WK"] = "Týden";
Calendar._TT["TIME"] = "Čas:";
