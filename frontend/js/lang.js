import $ from "jquery"

/**
 * Доступ к языковым текстовкам (предварительно должны быть прогружены в шаблон)
 */

/**
 * Набор текстовок
 */
export const msgs = {};

/**
 * Загрузка текстовок
 */
export function load(new_msgs) {
    $.extend(true, msgs, new_msgs);
}

/**
 * Отображение сообщения об ошибке
 */
export function get(name, replace) {
    if(msgs[name]) {
        let value = msgs[name];
        if(replace) {
            value = value.tr(replace);
        }
        return value;
    }
    return "";
}
