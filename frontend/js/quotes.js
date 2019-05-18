import * as Lang from "./lang"
import * as Msg from "./msg"
import $ from "jquery"
import * as Ajax from "./ajax"

export let g_selectedQuoteId = 0;

export function showAddForm() {
    $("#quotes_form_data").val("");
    $("#quotes_form_id").val("");
    $("#quotes_preview").html("").hide();
    $("#quotes_form_action").val("add");
    $("#quotes_form").jqmShow();
}

export function showEditForm(id) {
    $("#quotes_form_action").val("update");
    const data = $("#field_" + id + " .quotes_data").html();
    $("#quotes_form_data").val(data);
    $("#quotes_form_id").val(id);
    $("#quotes_preview").html("").hide();
    $("#quotes_form").jqmShow();
}

export function quotesPreview() {
    ls.tools.textPreview("quotes_form_data", false, "quotes_preview")
    $("#quotes_preview").show();
}

export function applyForm() {
    $("#quotes_form").jqmHide();

    const val = $("#quotes_form_action").val();
    if(val === "add") {
        this.addQuotes();
    } else if(val === "update") {
        this.updateQuotes();
    }
}

export function addQuotes() {
    const data_quote = $("#quotes_form_data").val();
    const url = aRouter["quotes"] + "edit";
    const params = {"action": "add", "data": data_quote};

    Ajax.ajax(url, params, function(data) {
        const counter = $("#quotes_count");
        counter.html(+counter.html() + 1);

        if(!data.bStateError) {
            window.location.href = aRouter["quotes"] + "#field_" + data.id;

            const trElement = $(
                "<tr id=\"field_" + data.id + "\" class=\"quote_element\">" +
                "    <td class=\"quotes_data\" id=\"quote_" + data.id + "\"></td>" +
                "        <td>" +
                "            <div class=\"quotes-actions\">" +
                "                <span style=\"float: left\">" +
                "                    <a href=\"#\" onclick=\"ls.quotes.showEditForm(" + data.id + "); return false;\"" +
                "                          title=\"" + Lang.get("quotes_update") + "\"><i class=\"fa fa-pencil\" style=\"float:left;\"" +
                "                          aria-hidden=\"true\"></i></a>" +
                "                    &nbsp;" +
                "                    <a href=\"/quotes/" + data.id + "\"" +
                "                          onclick=\"prompt('" + Lang.get("quotes_link") + "', '" + DIR_WEB_ROOT + "/quotes/" + data.id + "'); return false;\"" +
                "                          title=\"" + Lang.get("quotes_link") + "\"><i class=\"fa fa-hashtag\" aria-hidden=\"true\"></i></a>" +
                "                </span>" +
                "                <a href=\"#\" onclick=\"ls.quotes.deleteQuotes(" + data.id + "); return false;\"" +
                "                      title=\"" + Lang.get("quotes_delete") + "\"><i class=\"fa fa-trash\" style=\"float:right;\"" +
                "                                                                       aria-hidden=\"true\"></i></a>" +
                "            </div>" +
                "        </td>" +
                "    </tr>",
            );
            $("#quotes_list").append(trElement);

            ls.tools.textPreview("quotes_form_data", false, "quote_" + data.id)

            scrollToQuote(data.id);

            Msg.notice(data.sMsgTitle, data.sMsg);
        } else {
            Msg.error(data.sMsgTitle, data.sMsg);
        }
    });
}

export function updateQuotes() {
    const data_quote = $("#quotes_form_data").val();
    const id = $("#quotes_form_id").val();

    const url = aRouter["quotes"] + "edit";
    const params = {"action": "update", "id": id, "data": data_quote};

    Ajax.ajax(url, params, function(data) {
        if(!data.bStateError) {
            $("#field_" + id + " .quotes_data").html(data_quote);
            scrollToQuote(id);
            Msg.notice(data.sMsgTitle, data.sMsg);
        } else {
            Msg.error(data.sMsgTitle, data.sMsg);
        }
    });
}

export function deleteQuotes(id) {
    if(!confirm(Lang.get("quotes_delete_confirm"))) {
        return;
    }

    const url = aRouter["quotes"] + "edit";
    const params = {"action": "delete", "id": id};

    Ajax.ajax(url, params, function(data) {
        if(!data.bStateError) {
            let counter = $("#quotes_count");
            counter.html(+counter.html() - 1);

            $("#field_" + id).remove();
            Msg.notice(data.sMsgTitle, data.sMsg);
        } else {
            Msg.error(data.sMsgTitle, data.sMsg);
        }
    });
}

export function restoreQuotes(id) {
    const url = aRouter["quotes"] + "edit";
    const params = {"action": "restore", "id": id};

    Ajax.ajax(url, params, function(data) {
        if(!data.bStateError) {
            let counter = $("#quotes_count");
            counter.html(+counter.html() + 1);

            $("#field_" + id).remove();
            Msg.notice(data.sMsgTitle, data.sMsg);
        } else {
            Msg.error(data.sMsgTitle, data.sMsg);
        }
    });
}

export async function scrollToQuote(id) {
    const selectedQuote = $("#field_" + id);

    $("#field_" + g_selectedQuoteId).removeClass("info");
    selectedQuote.addClass("info");

    g_selectedQuoteId = id;

    $("html, body").animate({
        scrollTop: selectedQuote.offset().top - 200,
    }, 150);
}