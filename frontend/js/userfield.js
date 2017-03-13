import * as Lang from './lang'
import * as Msg from './msg'
import Emitter from './emitter'
import $ from 'jquery'
import * as Ajax from './ajax'

export let iCountMax = 2;

export function showAddForm() {
    $('#user_fields_form_name').val('');
    $('#user_fields_form_title').val('');
    $('#user_fields_form_id').val('');
    $('#user_fields_form_pattern').val('');
    $('#user_fields_form_type').val('');
    $('#user_fields_form_action').val('add');
    $('#userfield_form').jqmShow();
};

export function showEditForm(id) {
    $('#user_fields_form_action').val('update');
    var name = $('#field_' + id + ' .userfield_admin_name').text();
    var title = $('#field_' + id + ' .userfield_admin_title').text();
    var pattern = $('#field_' + id + ' .userfield_admin_pattern').text();
    var type = $('#field_' + id + ' .userfield_admin_type').text();
    $('#user_fields_form_name').val(name);
    $('#user_fields_form_title').val(title);
    $('#user_fields_form_pattern').val(pattern);
    $('#user_fields_form_type').val(type);
    $('#user_fields_form_id').val(id);
    $('#userfield_form').jqmShow();
};

export function applyForm() {
    $('#userfield_form').jqmHide();
    if ($('#user_fields_form_action').val() == 'add') {
        this.addUserfield();
    } else if ($('#user_fields_form_action').val() == 'update') {
        this.updateUserfield();
    }
};

export function addUserfield() {
    var name = $('#user_fields_form_name').val();
    var title = $('#user_fields_form_title').val();
    var pattern = $('#user_fields_form_pattern').val();
    var type = $('#user_fields_form_type').val();

    var url = aRouter['admin'] + 'userfields';
    var params = {'action': 'add', 'name': name, 'title': title, 'pattern': pattern, 'type': type};

    Emitter.emit('addUserfieldBefore');
    Ajax.ajax(url, params, function (data) {
        if (!data.bStateError) {
            liElement = $('<li id="field_' + data.id + '"><span class="userfield_admin_name"></span > / <span class="userfield_admin_title"></span> / <span class="userfield_admin_pattern"></span> / <span class="userfield_admin_type"></span>'
                + '<div class="userfield-actions"><a class="icon-edit" href="javascript:ls.userfieldshowEditForm(' + data.id + ')"></a> '
                + '<a class="icon-remove" href="javascript:ls.userfielddeleteUserfield(' + data.id + ')"></a></div>')
            ;
            $('#user_field_list').append(liElement);
            $('#field_' + data.id + ' .userfield_admin_name').text(name);
            $('#field_' + data.id + ' .userfield_admin_title').text(title);
            $('#field_' + data.id + ' .userfield_admin_pattern').text(pattern);
            $('#field_' + data.id + ' .userfield_admin_type').text(type);
            Msg.notice(data.sMsgTitle, data.sMsg);
            Emitter.emit('ls_userfield_add_userfield_after', [params, data], liElement);
        } else {
            Msg.error(data.sMsgTitle, data.sMsg);
        }
    });
};

export function updateUserfield() {
    var id = $('#user_fields_form_id').val();
    var name = $('#user_fields_form_name').val();
    var title = $('#user_fields_form_title').val();
    var pattern = $('#user_fields_form_pattern').val();
    var type = $('#user_fields_form_type').val();

    var url = aRouter['admin'] + 'userfields';
    var params = {'action': 'update', 'id': id, 'name': name, 'title': title, 'pattern': pattern, 'type': type};

    Emitter.emit('updateUserfieldBefore');
    Ajax.ajax(url, params, function (data) {
        if (!data.bStateError) {
            $('#field_' + id + ' .userfield_admin_name').text(name);
            $('#field_' + id + ' .userfield_admin_title').text(title);
            $('#field_' + id + ' .userfield_admin_pattern').text(pattern);
            $('#field_' + id + ' .userfield_admin_type').text(type);
            Msg.notice(data.sMsgTitle, data.sMsg);
            Emitter.emit('ls_userfield_update_userfield_after', [params, data]);
        } else {
            Msg.error(data.sMsgTitle, data.sMsg);
        }
    });
};

export function deleteUserfield(id) {
    if (!confirm(Lang.get('user_field_delete_confirm'))) {
        return;
    }

    var url = aRouter['admin'] + 'userfields';
    var params = {'action': 'delete', 'id': id};

    Emitter.emit('deleteUserfieldBefore');
    Ajax.ajax(url, params, function (data) {
        if (!data.bStateError) {
            $('#field_' + id).remove();
            Msg.notice(data.sMsgTitle, data.sMsg);
            Emitter.emit('ls_userfield_update_userfield_after', [params, data]);
        } else {
            Msg.error(data.sMsgTitle, data.sMsg);
        }
    });
};

export function addFormField() {
    var tpl = $('#profile_user_field_template').clone();
    /**
     * Находим доступный тип контакта
     */
    var value;
    tpl.find('select').find('option').each(function (k, v) {
        if (this.getCountFormField($(v).val()) < this.iCountMax) {
            value = $(v).val();
            return false;
        }
    }.bind(this));

    if (value) {
        tpl.find('select').val(value);
        $('#user-field-contact-contener').append(tpl.show());
    } else {
        Msg.error('', Lang.get('settings_profile_field_error_max', {count: this.iCountMax}));
    }
    return false;
};

export function changeFormField(obj) {
    var iCount = this.getCountFormField($(obj).val());
    if (iCount > this.iCountMax) {
        Msg.error('', Lang.get('settings_profile_field_error_max', {count: this.iCountMax}));
    }
};

export function getCountFormField(value) {
    var iCount = 0;
    $('#user-field-contact-contener').find('select').each(function (k, v) {
        if (value == $(v).val()) {
            iCount++;
        }
    });
    return iCount;
};

export function removeFormField(obj) {
    $(obj).parent('.js-user-field-item').detach();
    return false;
};
