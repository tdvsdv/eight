// ** I18N

// Calendar RU language
// Author: Pohresnik Dmitriy
// Encoding: ANSI as UTF-8
// Distributed under the same terms as the calendar itself.

// полное именование дней
Calendar._DN = new Array
("воскресенье",
"понедельник",
"вторник",
"среда",
"четверг",
"пятница",
"суббота",
"воскресенье");

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
("Вс",
"Пн",
"Вт",
"Ср",
"Чт",
"Пт",
"Сб",
"Вс");

// First day of the week. "0" means display Sunday first, "1" means display
// Monday first, etc.
Calendar._FD = 1;

// full month names
Calendar._MN = new Array
("январь",
"февраль",
"март",
"апрель",
"май",
"июнь",
"июль",
"август",
"сентябрь",
"октябрь",
"ноябрь",
"декабрь");

// short month names
Calendar._SMN = new Array
("янв",
"фев",
"мар",
"апр",
"май",
"июн",
"июл",
"авг",
"сен",
"окт",
"ноя",
"дек");

// tooltips
Calendar._TT = {};
Calendar._TT["INFO"] = "О календаре";

Calendar._TT["ABOUT"] =
"DHTML Date/Time Selector\n" +
"(c) dynarch.com 2002-2005 / Author: Mihai Bazon\n" + // don't translate this this ;-)
"Проверить последнюю версию: http://www.dynarch.com/projects/calendar/\n" +
"Распространяется под лицензией GNU LGPL.  See http://gnu.org/licenses/lgpl.html for details." +
"\n\n" +
"Выбор даты:\n" +
"- Используйте \xab, \xbb кнопки для выбора года\n" +
"- Используйте " + String.fromCharCode(0x2039) + ", " + String.fromCharCode(0x203a) + " кнопки для выбора месяца\n" +
"- Удерживайте нажатой кнопку мыши на любой из вышеперечисленных кнопок для быстрого выбора.";
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"Выбор времени:\n" +
"- Нажмите на соответствующем поле для увеличения значения\n" +
"- или Shift-клик - для уменьшения значения\n" +
"- или клик+перемещение для быстрого выбора.";

Calendar._TT["PREV_YEAR"] = "Пред. год (удерж. для меню)";
Calendar._TT["PREV_MONTH"] = "Пред. месяц (удерж. для меню)";
Calendar._TT["GO_TODAY"] = "Сегодня";
Calendar._TT["NEXT_MONTH"] = "След. месяц (удерж. для меню)";
Calendar._TT["NEXT_YEAR"] = "След. год (удерж. для меню)";
Calendar._TT["SEL_DATE"] = "Выбор даты";
Calendar._TT["DRAG_TO_MOVE"] = "Переместить";
Calendar._TT["PART_TODAY"] = " (сегодня)";

// the following is to inform that "%s" is to be the first day of week
// %s will be replaced with the day name.
Calendar._TT["DAY_FIRST"] = "Первый день недели: %s";

// This may be locale-dependent.  It specifies the week-end days, as an array
// of comma-separated numbers.  The numbers are from 0 to 6: 0 means Sunday, 1
// means Monday, etc.
Calendar._TT["WEEKEND"] = "0,6";

Calendar._TT["CLOSE"] = "Закрыть";
Calendar._TT["TODAY"] = "Сегодня";
Calendar._TT["TIME_PART"] = "(Shift-)клик или перемещ. для изм. знач.";

// date formats
Calendar._TT["DEF_DATE_FORMAT"] = "%d-%m-%Y";
Calendar._TT["TT_DATE_FORMAT"] = "%a, %b %e";

Calendar._TT["WK"] = "нед.";
Calendar._TT["TIME"] = "Время:";
