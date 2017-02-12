import * as Ajax from './ajax'

/**
 * Автокомплитер
 */

/**
 * Добавляет автокомплитер к полю ввода
 */
export function add(obj, sPath, multiple)
{
    if (multiple) {
        obj.bind("keydown", function (event) {
            if (event.keyCode === $.ui.keyCode.TAB && $(this).data("autocomplete").menu.active) {
                event.preventDefault();
            }
        })
            .autocomplete({
                source: function (request, response) {
                    Ajax.ajax(sPath, {value: ls.autocomplete.extractLast(request.term)}, function (data) {
                        response(data.aItems);
                    });
                },
                search: function () {
                    var term = ls.autocomplete.extractLast(this.value);
                    if (term.length < 2) {
                        return false;
                    }
                },
                focus: function () {
                    return false;
                },
                select: function (event, ui) {
                    var terms = ls.autocomplete.split(this.value);
                    terms.pop();
                    terms.push(ui.item.value);
                    terms.push("");
                    this.value = terms.join(", ");
                    return false;
                }
            });
    } else {
        obj.autocomplete({
            source: function (request, response) {
                ajax(sPath, {value: ls.autocomplete.extractLast(request.term)}, function (data) {
                    response(data.aItems);
                });
            }
        });
    }
}

export function split(val)
{
    return val.split(/,\s*/);
}

export function extractLast(term)
{
    return this.split(term).pop();
}
