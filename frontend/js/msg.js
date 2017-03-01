import $ from 'jquery'

/**
 * Управление всплывающими сообщениями
 */

/**
 * Опции
 */
export let options = {
    class_notice: 'n-notice',
    class_error: 'n-error'
};

/**
 * Отображение информационного сообщения
 */
export function notice(title, msg) {
    $.notifier.broadcast(title, msg, this.options.class_notice);
}

/**
 * Отображение сообщения об ошибке
 */
export function error(title, msg) {
    $.notifier.broadcast(title, msg, this.options.class_error);
}

export function notify(title, body) {
    if (title && body) {
        // Давайте проверим, поддерживает ли браузер уведомления
        if (!("Notification" in window)) {
            alert("Ваш браузер не поддерживает HTML5 Notifications");
        }
        // Теперь давайте проверим есть ли у нас разрешение для отображения уведомления

        else if (Notification.permission === "granted") {
            // Если все в порядке, то создадим уведомление
            var notification1 = new Notification(title, {
                'body': body,
            });
        }
        // В противном случае, мы должны спросить у пользователя разрешение

        else if (Notification.permission === 'default') {
            Notification.requestPermission(function (permission) {

                    // Не зависимо от ответа, сохраняем его в настройках
                    if (!('permission' in Notification)) {
                        Notification.permission = permission;
                    }
                    // Если разрешение получено, то создадим уведомление
                    if (permission === "granted") {
                        var notification1 = new Notification(title, {
                            'body': body,
                        });
                    }
                }
            );
        }
    }
}
