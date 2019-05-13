import * as Lang from './lang'
import * as Stream from './stream'
import * as Msg from './msg'
import Emitter from './emitter'
import $ from 'jquery'
import './jquery/jquery.Jcrop'
import * as Ajax from './ajax'

/**
 * Добавление в друзья
 */
export function addFriend(obj, idUser, sAction) {
    if (sAction != 'link' && sAction != 'accept') {
        var sText = $('#add_friend_text').val();
        $('#add_friend_form').children().each(function (i, item) {
            $(item).attr('disabled', 'disabled')
        });
    } else {
        var sText = '';
    }

    if (sAction == 'accept') {
        var url = aRouter.profile + 'ajaxfriendaccept/';
    } else {
        var url = aRouter.profile + 'ajaxfriendadd/';
    }

    var params = {idUser: idUser, userText: sText};

    Emitter.emit('addFriendBefore');
    Ajax.ajax(url, params, function (result) {
        $('#add_friend_form').children().each(function (i, item) {
            $(item).removeAttr('disabled')
        });
        if (!result) {
            Msg.error('Error', 'Please try again later');
        }
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            Msg.notice(null, result.sMsg);
            $('#add_friend_form').jqmHide();
            $('#add_friend_item').remove();
            $('#profile_actions').prepend($(result.sToggleText));
            Emitter.emit('ls_user_add_friend_after', [idUser, sAction, result], obj);
        }
    });
    return false;
}

/**
 * Удаление из друзей
 */
export function removeFriend(obj, idUser, sAction) {
    var url = aRouter.profile + 'ajaxfrienddelete/';
    var params = {idUser: idUser, sAction: sAction};

    Emitter.emit('removeFriendBefore');
    Ajax.ajax(url, params, function (result) {
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            Msg.notice(null, result.sMsg);
            $('#delete_friend_item').remove();
            $('#profile_actions').prepend($(result.sToggleText));
            Emitter.emit('ls_user_remove_friend_after', [idUser, sAction, result], obj);
        }
    });
    return false;
}

/**
 * Загрузка временной аватарки
 * @param form
 * @param input
 */
export function uploadAvatar(form, input) {
    if (!form && input) {
        var form = $('<form method="post" enctype="multipart/form-data"></form>').css({
            'display': 'none'
        }).appendTo('body');
        var clone = input.clone(true);
        input.hide();
        clone.insertAfter(input);
        input.appendTo(form);
    }

    Ajax.ajaxSubmit(aRouter['settings'] + 'profile/upload-avatar/', form, function (data) {
        if (data.bStateError) {
            Msg.error(data.sMsgTitle, data.sMsg);
        } else {
            this.showResizeAvatar(data.sTmpFile);
        }
    }.bind(this));
}

/**
 * Показывает форму для ресайза аватарки
 * @param sImgFile
 */
export function showResizeAvatar(sImgFile) {
    if (this.jcropAvatar) {
        this.jcropAvatar.destroy();
    } else {
        this.jcropAvatar = null;
    }
    $('#avatar-resize-original-img').attr('src', sImgFile + '?' + Math.random());
    $('#avatar-resize').jqmShow();
    var $this = this;
    $('#avatar-resize-original-img').Jcrop({
        aspectRatio: 1,
        minSize: [32, 32]
    }, function () {
        $this.jcropAvatar = this;
        this.setSelect([0, 0, 500, 500]);
    });
}

/**
 * Выполняет ресайз аватарки
 */
export function resizeAvatar() {
    if (!this.jcropAvatar) {
        return false;
    }
    var url = aRouter.settings + 'profile/resize-avatar/';
    var params = {size: this.jcropAvatar.tellSelect()};

    Emitter.emit('resizeAvatarBefore');
    Ajax.ajax(url, params, function (result) {
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            $('#avatar-img').attr('src', result.sFile + '?' + Math.random());
            $('#avatar-resize').jqmHide();
            $('#avatar-remove').show();
            $('#avatar-upload').text(result.sTitleUpload);
            Emitter.emit('ls_user_resize_avatar_after', [params, result]);
        }
    });

    return false;
}

/**
 * Удаление аватарки
 */
export function removeAvatar() {
    var url = aRouter.settings + 'profile/remove-avatar/';
    var params = {};

    Emitter.emit('removeAvatarBefore');
    Ajax.ajax(url, params, function (result) {
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            $('#avatar-img').attr('src', result.sFile + '?' + Math.random());
            $('#avatar-remove').hide();
            $('#avatar-upload').text(result.sTitleUpload);
            Emitter.emit('ls_user_remove_avatar_after', [params, result]);
        }
    });

    return false;
}

/**
 * Отмена ресайза аватарки, подчищаем временный данные
 */
export function cancelAvatar() {
    var url = aRouter.settings + 'profile/cancel-avatar/';
    var params = {};

    Emitter.emit('cancelAvatarBefore');
    Ajax.ajax(url, params, function (result) {
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            $('#avatar-resize').jqmHide();
            Emitter.emit('ls_user_cancel_avatar_after', [params, result]);
        }
    });

    return false;
}

/**
 * Загрузка временной фотки
 * @param form
 * @param input
 */
export function uploadFoto(form, input) {
        var form = $('<form method="post" enctype="multipart/form-data"><input type="file" name="foto" id="upload-foto-input" /></form>').css({
            'display': 'none'
        }).appendTo('body');
        // var clone = input.clone(true);
        // input.hide();
        // clone.insertAfter(input);
        // input.appendTo(form);
    $("#upload-foto-input")[0].click()
    $("#upload-foto-input").change(()=> {
        Ajax.ajaxSubmit(aRouter['settings'] + 'profile/upload-foto/', form, function (data) {
            if (data.bStateError) {
                Msg.error(data.sMsgTitle, data.sMsg);
            } else {
                this.showResizeFoto(data.sTmpFile, data.iHeight);
            }
        }.bind(this));
    })
}

/**
 * Показывает форму для ресайза фотки
 * @param sImgFile
 */
export function showResizeFoto(sImgFile, h) {
    if (this.jcropFoto) {
        this.jcropFoto.destroy();
    } else {
        this.jcropFoto = null;
    }
    $('#foto-resize-original-img').attr('src', sImgFile + '?' + Math.random());
    $('#foto-resize').jqmShow();
    var $this = this;
    $('#foto-resize-original-img').Jcrop({
        minSize: [400, h],
        maxSize: [400, h],
    }, function () {
        $this.jcropFoto = this;
        this.setSelect([0, 0, 400, 40]);
    });
}

/**
 * Выполняет ресайз фотки
 */
export function resizeFoto() {
    if (!this.jcropFoto) {
        return false;
    }
    var url = aRouter.settings + 'profile/resize-foto/';
    var params = {size: this.jcropFoto.tellSelect()};

    Emitter.emit('resizeFotoBefore');
    Ajax.ajax(url, params, function (result) {
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            $('#foto-img').attr('src', result.sFile + '?' + Math.random());
            $('#foto-resize').jqmHide();
            $('#foto-remove').show();
            $('#foto-upload').text(result.sTitleUpload);
            Emitter.emit('ls_user_resize_foto_after', [params, result]);
            location.reload()
        }
    });

    return false;
}

/**
 * Удаление фотки
 */
export function removeFoto() {
    var url = aRouter.settings + 'profile/remove-foto/';
    var params = {};

    Emitter.emit('removeFotoBefore');
    Ajax.ajax(url, params, function (result) {
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            $('#foto-img').attr('src', result.sFile + '?' + Math.random());
            $('#foto-remove').hide();
            $('#foto-upload').text(result.sTitleUpload);
            Emitter.emit('ls_user_remove_foto_after', [params, result]);
            location.reload()
        }
    });

    return false;
}

/**
 * Отмена ресайза фотки, подчищаем временный данные
 */
export function cancelFoto() {
    var url = aRouter.settings + 'profile/cancel-foto/';
    var params = {};

    Emitter.emit('cancelFotoBefore');
    Ajax.ajax(url, params, function (result) {
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            $('#foto-resize').jqmHide();
            Emitter.emit('ls_user_cancel_foto_after', [params, result]);
        }
    });

    return false;
}

/**
 * Валидация полей формы при регистрации
 * @param aFields
 */
export function validateRegistrationFields(aFields, sForm) {
    var url = aRouter.registration + 'ajax-validate-fields/';
    var params = {fields: aFields};
    if (typeof(sForm) == 'string') {
        sForm = $('#' + sForm);
    }

    //Emitter.emit('validateRegistrationFieldsBefore');
    Ajax.ajax(url, params, function (result) {
        if (!sForm) {
            sForm = $('body'); // поиск полей по всей странице
        }
        $.each(aFields, function (i, aField) {
            if (result.aErrors && result.aErrors[aField.field][0]) {
                sForm.find('.validate-error-field-' + aField.field).removeClass('validate-error-hide').addClass('validate-error-show').text(result.aErrors[aField.field][0]);
                sForm.find('.validate-ok-field-' + aField.field).hide();
            } else {
                sForm.find('.validate-error-field-' + aField.field).removeClass('validate-error-show').addClass('validate-error-hide');
                sForm.find('.validate-ok-field-' + aField.field).show();
            }
        });
        Emitter.emit('ls_user_validate_registration_fields_after', [aFields, sForm, result]);
    });
}

/**
 * Валидация конкретного поля формы
 * @param sField
 * @param sValue
 * @param aParams
 */
export function validateRegistrationField(sField, sValue, sForm, aParams) {
    var aFields = [];
    aFields.push({field: sField, value: sValue, params: aParams || {}});
    this.validateRegistrationFields(aFields, sForm);
}

/**
 * Ajax регистрация пользователя с проверкой полей формы
 * @param form
 */
export function registration(form) {
    var url = aRouter.registration + 'ajax-registration/';

    this.formLoader(form);
    Emitter.emit('registrationBefore');
    Ajax.ajaxSubmit(url, form, function (result) {
        this.formLoader(form, true);
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            if (typeof(form) == 'string') {
                form = $('#' + form);
            }
            form.find('.validate-error-show').removeClass('validate-error-show').addClass('validate-error-hide');
            if (result.aErrors) {
                $.each(result.aErrors, function (sField, aErrors) {
                    if (aErrors[0]) {
                        form.find('.validate-error-field-' + sField).removeClass('validate-error-hide').addClass('validate-error-show').text(aErrors[0]);
                    }
                });
            } else {
                if (result.sMsg) {
                    Msg.notice(null, result.sMsg);
                }
                if (result.sUrlRedirect) {
                    window.location = result.sUrlRedirect;
                }
            }
            Emitter.emit('ls_user_registration_after', [form, result]);
        }
    }.bind(this));
}

/**
 * Ajax авторизация пользователя с проверкой полей формы
 * @param form
 */
export function login(form) {
    var url = aRouter.login + 'ajax-login/';

    this.formLoader(form);
    Emitter.emit('loginBefore');
    Ajax.ajaxSubmit(url, form, function (result) {
        this.formLoader(form, true);
        if (typeof(form) == 'string') {
            form = $('#' + form);
        }
        form.find('.validate-error-show').removeClass('validate-error-show').addClass('validate-error-hide');

        if (result.bStateError) {
            form.find('.validate-error-login').removeClass('validate-error-hide').addClass('validate-error-show').html(result.sMsg);
        } else {
            if (result.sMsg) {
                Msg.notice(null, result.sMsg);
            }
            if (result.sUrlRedirect) {
                window.location = result.sUrlRedirect;
            }
            if (result.sKey) {
                localStorage.setItem("sKey", result.sKey)
            }
            Emitter.emit('ls_user_login_after', [form, result]);
        }
    }.bind(this));
}

/**
 * Показывает лоадер в полях формы
 * @param form
 * @param bHide
 */
export function formLoader(form, bHide) {
    if (typeof(form) == 'string') {
        form = $('#' + form);
    }
    form.find('input[type="text"], input[type="password"]').each(function (k, v) {
        if (bHide) {
            $(v).removeClass('loader');
        } else {
            $(v).addClass('loader');
        }
    });
}

/**
 * Ajax запрос на смену пароля
 * @param form
 */
export function reminder(form) {
    var url = aRouter.login + 'ajax-reminder/';

    this.formLoader(form);
    Emitter.emit('reminderBefore');
    Ajax.ajaxSubmit(url, form, function (result) {
        this.formLoader(form, true);
        if (typeof(form) == 'string') {
            form = $('#' + form);
        }
        form.find('.validate-error-show').removeClass('validate-error-show').addClass('validate-error-hide');

        if (result.bStateError) {
            form.find('.validate-error-reminder').removeClass('validate-error-hide').addClass('validate-error-show').text(result.sMsg);
        } else {
            form.find('input').val('');
            if (result.sMsg) {
                Msg.notice(null, result.sMsg);
            }
            if (result.sUrlRedirect) {
                window.location = result.sUrlRedirect;
            }
            Emitter.emit('ls_user_reminder_after', [form, result]);
        }
    }.bind(this));
}

/**
 * Ajax запрос на ссылку активации
 * @param form
 */
export function reactivation(form) {
    var url = aRouter.login + 'ajax-reactivation/';

    Emitter.emit('reactivationBefore');
    Ajax.ajaxSubmit(url, form, function (result) {
        if (typeof(form) == 'string') {
            form = $('#' + form);
        }
        form.find('.validate-error-show').removeClass('validate-error-show').addClass('validate-error-hide');

        if (result.bStateError) {
            form.find('.validate-error-reactivation').removeClass('validate-error-hide').addClass('validate-error-show').text(result.sMsg);
        } else {
            form.find('input').val('');
            if (result.sMsg) {
                Msg.notice(null, result.sMsg);
            }
            Emitter.emit('ls_user_reactivation_after', [form, result]);
        }
    });
}

/**
 * Поиск пользователей
 */
export function searchUsers(form) {
    var url = aRouter['people'] + 'ajax-search/';
    var inputSearch = $('#' + form).find('input');
    inputSearch.addClass('loader');

    Emitter.emit('searchUsersBefore');
    Ajax.ajaxSubmit(url, form, function (result) {
        inputSearch.removeClass('loader');
        if (result.bStateError) {
            $('#users-list-search').hide();
            $('#users-list-original').show();
        } else {
            $('#users-list-original').hide();
            $('#users-list-search').html(result.sText).show();
            Emitter.emit('ls_user_search_users_after', [form, result]);
        }
    });
}

export function searchBlogUsers(form) {
    var url = aRouter['blog'] + 'ajax-search/';
    var inputSearch = $('#' + form).find('input');
    inputSearch.addClass('loader');

    Emitter.emit('searchUsersBefore');
    Ajax.ajaxSubmit(url, form, function (result) {
        inputSearch.removeClass('loader');
        if (result.bStateError) {
            $('#users-list-search').addClass("hidden");
            $('#users-list-original').removeClass("hidden");
        } else {
            $('#users-list-original').addClass("hidden");
            $('#users-list-search').html(result.sText).removeClass("hidden");
            Emitter.emit('ls_user_search_users_after', [form, result]);
        }
    });
}



/**
 * Поиск пользователей по началу логина
 */
export function searchUsersByPrefix(sPrefix, obj) {
    obj = $(obj);
    var url = aRouter['people'] + 'ajax-search/';
    var params = {user_login: sPrefix, isPrefix: 1};
    $('#search-user-login').addClass('loader');

    Emitter.emit('searchUsersByPrefixBefore');
    Ajax.ajax(url, params, function (result) {
        $('#search-user-login').removeClass('loader');
        $('#user-prefix-filter').find('.active').removeClass('active');
        obj.parent().addClass('active');
        if (result.bStateError) {
            $('#users-list-search').hide();
            $('#users-list-original').show();
        } else {
            $('#users-list-original').hide();
            $('#users-list-search').html(result.sText).show();
            Emitter.emit('ls_user_search_users_by_prefix_after', [sPrefix, obj, result]);
        }
    });
    return false;
}

export function searchBlogUsersByPrefix(sPrefix, obj) {
    obj = $(obj);
    var url = aRouter['blog'] + 'ajax-search/';
    var params = {user_login: sPrefix, isPrefix: 1};
    $('#search-user-login').addClass('loader');

    Emitter.emit('searchUsersByPrefixBefore');
    Ajax.ajax(url, params, function (result) {
        $('#search-user-login').removeClass('loader');
        $('#user-prefix-filter').find('.active').removeClass('active');
        obj.parent().addClass('active');
        if (result.bStateError) {
            $('#users-list-search').hide();
            $('#users-list-original').show();
        } else {
            $('#users-list-original').hide();
            $('#users-list-search').html(result.sText).show();
            Emitter.emit('ls_user_search_users_by_prefix_after', [sPrefix, obj, result]);
        }
    });
    return false;
}

/**
 * Подписка
 */
export function followToggle(obj, iUserId) {
    if ($(obj).hasClass('followed')) {
        Stream.unsubscribe(iUserId);
        $(obj).toggleClass('followed').text(Lang.get('profile_user_follow'));
    } else {
        Stream.subscribe(iUserId);
        $(obj).toggleClass('followed').text(Lang.get('profile_user_unfollow'));
    }
    return false;
}

export function banUser(form) {
    // Ajax.ajaxSubmit('ban', form)
    let iUnban = $(form).find(`[name="iUnban"]`).val()
    let iUserId = $(form).find(`[name="iUserId"]`).val()
    let iBanHours = $(form).find(`[name="iBanHours"]`).val()
    let sBanComment = $(form).find(`[name="sBanComment"]`).val()
    Ajax.asyncAjax('ban', {
        iUnban: iUnban,
        iUserId: iUserId,
        iBanHours: iBanHours,
        sBanComment: sBanComment},
        function() {location.reload()})
}
