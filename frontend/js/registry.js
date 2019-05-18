/**
 * Функционал хранения js данных
 */


export const aData = {};

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
