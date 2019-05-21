import * as Msg from "./msg"
import Emitter from "./emitter"
import $ from "jquery"
import * as Ajax from "./ajax"

/**
 * Добавление в избранное
 */

/**
 * Опции
 */
export const options = {
    active: "active",
    type: {
        topic: {
            url: aRouter["ajax"] + "favourite/topic/",
            targetName: "idTopic",
        },
        talk: {
            url: aRouter["ajax"] + "favourite/talk/",
            targetName: "idTalk",
        },
        comment: {
            url: aRouter["ajax"] + "favourite/comment/",
            targetName: "idComment",
        },
    },
};

/**
 * Переключение избранного
 */
export function toggle(idTarget, objFavourite, type) {
    if(!this.options.type[type]) {
        return false;
    }

    this.objFavourite = $(objFavourite);

    const params = {};
    params["type"] = !this.objFavourite.hasClass(this.options.active);
    params[this.options.type[type].targetName] = idTarget;

    Emitter.emit("favourite_toggle_before");
    Ajax.ajax(this.options.type[type].url, params, function(result) {
        $(this).trigger("toggle", [idTarget, objFavourite, type, params, result]);
        if(result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            Msg.notice(null, result.sMsg);
            this.objFavourite.removeClass(this.options.active);
            if(result.bState) {
                this.objFavourite.addClass(this.options.active);
                this.showTags(type, idTarget);
            } else {
                this.hideTags(type, idTarget);
            }
            if(type === "comment") {
                $(objFavourite).parent().find(".favourite-count")[0].innerText = result.iCount ? result.iCount : "";
            }
            Emitter.emit("favourite_toggle_after", [idTarget, objFavourite, type, params, result], this);
        }
    }.bind(this));
    return false;
}

export function showEditTags(idTarget, type, obj) {
    const form = $("#favourite-form-tags");
    const target_type_sel = $("#favourite-form-tags-target-type");
    const target_id_sel = $("#favourite-form-tags-target-id");
    target_type_sel.val(type);
    target_id_sel.val(idTarget);
    var text = "";
    var tags = $(".js-favourite-tags-" + target_type_sel.val() + "-" + target_id_sel.val());
    tags.find(".js-favourite-tag-user a").each(function(k, tag) {
        if(text) {
            text = text + ", " + $(tag).text();
        } else {
            text = $(tag).text();
        }
    });
    $("#favourite-form-tags-tags").val(text);
    //$(obj).parents('.js-favourite-insert-after-form').after(form);
    form.jqmShow();

    return false;
}

export function hideEditTags() {
    $("#favourite-form-tags").jqmHide();
    return false;
}

export function saveTags(form) {
    const url = aRouter["ajax"] + "favourite/save-tags/";
    Emitter.emit("favourite_savetags_before");
    Ajax.ajaxSubmit(url, $(form), function(result) {
        if(result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            this.hideEditTags();
            const type = $("#favourite-form-tags-target-type").val();
            const tags = $(".js-favourite-tags-" + type + "-" + $("#favourite-form-tags-target-id").val());
            tags.find(".js-favourite-tag-user").detach();
            const edit = tags.find(".js-favourite-tag-edit");
            $.each(result.aTags, function(k, v) {
                edit.before("<li class=\"" + type + "-tags-user js-favourite-tag-user\">, <a rel=\"tag\" href=\"" + v.url + "\">" + v.tag + "</a></li>");
            });

            Emitter.emit("favourite_savetags_after", [form, result], this);
        }
    }.bind(this));
    return false;
}

export function hideTags(targetType, targetId) {
    const tags = $(".js-favourite-tags-" + targetType + "-" + targetId);
    tags.find(".js-favourite-tag-user").detach();
    tags.find(".js-favourite-tag-edit").hide();
    this.hideEditTags();
}

export function showTags(targetType, targetId) {
    $(".js-favourite-tags-" + targetType + "-" + targetId).find(".js-favourite-tag-edit").show();
}
