/**
 * Методы таймера например, запуск функии через интервал
 */

export var aTimers = {};

/**
 * Запуск метода через определенный период, поддерживает пролонгацию
 */
export function run(fMethod, sUniqKey, aParams, iTime) {
    iTime = iTime || 1500;
    aParams = aParams || [];
    sUniqKey = sUniqKey || Math.random();

    if (aTimers[sUniqKey]) {
        clearTimeout(aTimers[sUniqKey]);
        aTimers[sUniqKey] = null;
    }
    let timeout = setTimeout(function () {
        clearTimeout(aTimers[sUniqKey]);
        aTimers[sUniqKey] = null;
        fMethod.apply(this, aParams);
    }.bind(this), iTime);
    aTimers[sUniqKey] = timeout;
}
