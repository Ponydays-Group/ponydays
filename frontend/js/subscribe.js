import * as Msg from "./msg"
import Emitter from "./emitter"
import * as Ajax from "./ajax"

/**
 * Подписка
 */

/**
 * Подписка/отписка
 */
export function toggle(sTargetType, iTargetId, sMail, iValue) {
    const url = aRouter["subscribe"] + "ajax-subscribe-toggle/";
    const params = {target_type: sTargetType, target_id: iTargetId, mail: sMail, value: iValue};
    Emitter.emit("toggleBefore");
    Ajax.ajax(url, params, function(result) {
        if(result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            Msg.notice(null, result.sMsg);
            Emitter.emit("ls_subscribe_toggle_after", [sTargetType, iTargetId, sMail, iValue, result]);
        }
    });
    return false;
}
