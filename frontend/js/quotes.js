import * as Lang from './lang'
import * as Msg from './msg'
import Emitter from './emitter'
import $ from 'jquery'
import * as Ajax from './ajax'

export function showAddForm() {
	$('#quotes_form_data').val('');
	$('#quotes_form_id').val('');
	$('#quotes_preview').html('').hide();
	$('#quotes_form_action').val('add');
	$('#quotes_form').jqmShow();
}

export function showEditForm(id) {
	$('#quotes_form_action').val('update');
	let data = $('#field_' + id + ' .quotes_data').html();
	$('#quotes_form_data').val(data);
	$('#quotes_form_id').val(id);
	$('#quotes_preview').html('').hide();
	$('#quotes_form').jqmShow();
}

export function quotesPreview() {
	let data = $('#quotes_form_data').val();
	$('#quotes_preview').html(data).show();
}

export function applyForm() {
	$('#quotes_form').jqmHide();

	let val = $('#quotes_form_action').val();
	if (val === 'add') {
		this.addQuotes();
	} else if (val === 'update') {
		this.updateQuotes();
	}
}

export function addQuotes() {
	let data_quote = $('#quotes_form_data').val();
	let url = aRouter['quotes'] + 'list';
	let params = {'action': 'add', 'data': data_quote};

	Ajax.ajax(url, params, function (data) {
		g_quotesCount++;

		if (!data.bStateError) {
			let trElement = $(
				'<tr id="field_' + data.id + '">\n' +
				'    <td>' + g_quotesCount + '</td>\n' +
				'    <td class="quotes_data">' + data_quote + '</td>\n' +
				'    <td>\n' +
				'        <div class="quotes-actions">\n' +
				'            <a href="#" onclick="ls.quotes.showEditForm(' + data.id + '); return false;" title="' + Lang.get('quotes_update') + '"><i class="fa fa-pencil" style="float: left;" aria-hidden="true"></i></a>\n' +
				'            <a href="#" onclick="ls.quotes.deleteQuotes(' + data.id + '); return false;" title="' + Lang.get('quotes_delete') + '"><i class="fa fa-trash" style="float: right;" aria-hidden="true"></i></a>\n' +
				'        </div>\n' +
				'    </td>\n' +
				'</tr>'
			);

			$('#quotes_list').append(trElement);
			scrollToQuote(data.id);

			Msg.notice(data.sMsgTitle, data.sMsg);
		} else {
			Msg.error(data.sMsgTitle, data.sMsg);
		}
	});
}

export function updateQuotes() {
	let data_quote = $('#quotes_form_data').val();
	let id = $('#quotes_form_id').val();

	let url = aRouter['quotes'] + 'list';
	let params = {'action': 'update', 'id': id, 'data': data_quote};

	Ajax.ajax(url, params, function (data) {
		if (!data.bStateError) {
			$('#field_' + id + ' .quotes_data').html(data_quote);
			scrollToQuote(id);
			Msg.notice(data.sMsgTitle, data.sMsg);
		} else {
			Msg.error(data.sMsgTitle, data.sMsg);
		}
	});
}

export function deleteQuotes(id) {
	console.log(Lang.get('quotes_delete_confirm'));

	if (!confirm(Lang.get('quotes_delete_confirm'))) {
		return;
	}

	let url = aRouter['quotes'] + 'list';
	let params = {'action': 'delete', 'id': id};

	Ajax.ajax(url, params, function (data) {
		if (!data.bStateError) {
			$('#field_' + id).remove();
			Msg.notice(data.sMsgTitle, data.sMsg);
		} else {
			Msg.error(data.sMsgTitle, data.sMsg);
		}
	});
}

export function scrollToQuote(id) {
	let selectedQuote = $('#field_' + id)

	$('#field_' + g_selectedQuoteId).removeClass('info');
	selectedQuote.addClass('info');

	g_selectedQuoteId = id;

	$('html, body').animate({
		scrollTop: selectedQuote.offset().top - 200
	}, 150);
}

export function getCountQuotes(value) {
	/*
	$('#user-field-contact-contener').find('select').each(function (k, v) {
		if (value == $(v).val()) {
			iCount++;
		}
	});
	*/

	return g_quotesCount;
}