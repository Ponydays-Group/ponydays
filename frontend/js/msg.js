import $ from "jquery"
import "./jquery/jquery.notifier"

/**
 * Управление всплывающими сообщениями
 */

/**
 * Опции
 */
export const options = {
    class_notice: "n-notice",
    class_error: "n-error",
};

/**
 * Отображение информационного сообщения
 */
export function notice(title, msg, url, blank) {
    $.notifier.broadcast(title, msg, this.options.class_notice, url, blank);
}

/**
 * Отображение сообщения об ошибке
 */
export function error(title, msg) {
    $.notifier.broadcast(title, msg, this.options.class_error);
}

export function notify(title, body) {
    if(title && body) {
        // Давайте проверим, поддерживает ли браузер уведомления
        if(!("Notification" in window)) {
            alert("Ваш браузер не поддерживает HTML5 Notifications");
        }
        // Теперь давайте проверим есть ли у нас разрешение для отображения уведомления

        else if(Notification.permission === "granted") {
            // Если все в порядке, то создадим уведомление
            const notification1 = new Notification(title, {
                "body": body,
            });
        }
        // В противном случае, мы должны спросить у пользователя разрешение

        else if(Notification.permission === "default") {
            Notification.requestPermission(function(permission) {

                    // Не зависимо от ответа, сохраняем его в настройках
                    if(!("permission" in Notification)) {
                        Notification.permission = permission;
                    }
                    // Если разрешение получено, то создадим уведомление
                    if(permission === "granted") {
                        const notification1 = new Notification(title, {
                            "body": body,
                        });
                    }
                },
            );
        }
    }
}
