/**
 * Функционал хранения js данных
 */


export var aData = {};

/**
 * Сохранение
 */
export function set(sName, data) {
    aData[sName] = data;
}

/**
 * Получение
 */
export function get(sName) {
    return aData[sName];
}
