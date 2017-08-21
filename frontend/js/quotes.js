import * as Lang from './lang'
import * as Msg from './msg'
import Emitter from './emitter'
import $ from 'jquery'
import * as Ajax from './ajax'

export let g_selectedQuoteId = 0;

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
	let url = aRouter['quotes'] + 'edit';
	let params = {'action': 'add', 'data': data_quote};

	Ajax.ajax(url, params, function (data) {
		let counter = $('#quotes_count');
		counter.html(+counter.html() + 1);

		if (!data.bStateError) {
			window.location.href = aRouter['quotes'] + "#field_" + data.id;

			let trElement = $(
				'<tr id="field_' + data.id + '" class="quote_element">\n' +
				'    <td class="quotes_data">' + data_quote + '</td>\n' +
				'    <td>\n' +
				'        <div class="quotes-actions">\n' +
				'           <a href="#" onclick="prompt("' + Lang.get('quotes_link') + '", ' + DIR_WEB_ROOT + '/quotes/' + data.id + '); return false;" title="' + Lang.get('quotes_link') + '"><i class="fa fa-hashtag" aria-hidden="true"></i></a>&nbsp;&nbsp;\n' +
				'			<a href="#" onclick="ls.quotes.showEditForm(' + data.id + '); return false;" title="' + Lang.get('quotes_update') + '"><i class="fa fa-pencil" style="float: left;" aria-hidden="true"></i></a>\n' +
				'           <a href="#" onclick="ls.quotes.deleteQuotes(' + data.id + '); return false;" title="' + Lang.get('quotes_delete') + '"><i class="fa fa-trash" style="float: right;" aria-hidden="true"></i></a>\n' +
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

	let url = aRouter['quotes'] + 'edit';
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

	let url = aRouter['quotes'] + 'edit';
	let params = {'action': 'delete', 'id': id};

	Ajax.ajax(url, params, function (data) {
		if (!data.bStateError) {
			let counter = $('#quotes_count');
			counter.html(+counter.html() - 1);

			$('#field_' + id).remove();
			Msg.notice(data.sMsgTitle, data.sMsg);
		} else {
			Msg.error(data.sMsgTitle, data.sMsg);
		}
	});
}

export function restoreQuotes(id) {
	let url = aRouter['quotes'] + 'edit';
	let params = {'action': 'restore', 'id': id};

	Ajax.ajax(url, params, function (data) {
		if (!data.bStateError) {
			let counter = $('#quotes_count');
			counter.html(+counter.html() + 1);

			$('#field_' + id).remove();
			Msg.notice(data.sMsgTitle, data.sMsg);
		} else {
			Msg.error(data.sMsgTitle, data.sMsg);
		}
	});
}

export async function scrollToQuote(id) {
	let selectedQuote = $('#field_' + id)

	$('#field_' + g_selectedQuoteId).removeClass('info');
	selectedQuote.addClass('info');

	g_selectedQuoteId = id;

	$('html, body').animate({
		scrollTop: selectedQuote.offset().top - 200
	}, 150);
}