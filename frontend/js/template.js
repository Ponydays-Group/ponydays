import * as Talk from './talk'
import * as Comments from './comments'
import * as Registry from './registry'
import * as Toolbar from './toolbar'
import * as Autocomplete from './autocomplete'
import * as Blocks from './blocks'
import * as Hook from './hook'

function showFloatBlock($) {
    $.browser.isMobileDevice = /android|webos|iphone|ipad|ipod|blackberry/i.test(navigator.userAgent.toLowerCase());
    if ($.browser.isMobileDevice || $(window).width() < 1024 || $(window).height() < 500 || screen.width < 1024) {
        return
    }
    var showFloat = false;
    var reinit = function() {
        var floatBlock = $('.block-type-stream');
        if ($(window).width() < 1024 || $(window).height() < 500 || screen.width < 1024) {
            if (showFloat) {
                floatBlock.removeClass('stream-fixed');
                showFloat = false;
            }
            return;
        }
        var sidebar = $('#sidebar');
        var last_block = $($(".block")[$(".block").length - 1])
        var bottomPos = last_block.offset().top + last_block.outerHeight();
        if (showFloat) {
            bottomPos += floatBlock.outerHeight() + 20;
        }
        if (window.pageYOffset > bottomPos) {
            if (!showFloat) {
                floatBlock.addClass('stream-fixed');
                floatBlock.css("width", sidebar.width());
                //floatBlock.offset({left: Math.min($(window).width - 360, $(window).width / 2 + 1280/2 - 360)});
                floatBlock.animate({
                    opacity: 0
                }, 0, function() {
                    floatBlock.animate({
                        opacity: 1
                    }, 350)
                });
                showFloat = true;
            }
        } else {
            if (showFloat) {
                floatBlock.removeClass('stream-fixed');
                showFloat = false;
            }
        }
    }
    $(window).bind('scroll resize', reinit);
    reinit();
}

export function updateImgs(){
  $(".spoiler-body img").each(
    function(k,v){
      if(!v.getAttribute("data-src")){
        console.log(v.src, v)
        v.setAttribute("data-src", v.src)
        v.src="#"
      }
    }
  )
}

export default function init() {
    jQuery(document).ready(function($) {
        // Хук начала инициализации javascript-составляющих шаблона
        Hook.run('ls_template_init_start', [], window);

        // updateImgs()
        $("#image-modal").click(function(){$("#image-modal").css("display", "none")})
        $("#image-modal-img").click(function(){$("#image-modal").css("display", "none")})

        $('html').removeClass('no-js');

        window.resize_sidebar = function() {
          if ($("#content").offset().top == $("#sidebar").offset().top) {
            $("#sidebar").css("height", $("#wrapper").height() > $("#sidebar").height() ? $("#wrapper").height() : null)
          }
        }
        $(window).on('resize', window.resize_sidebar)

        // Всплывающие окна
        $('#window_login_form').jqm();
        $('#blog_delete_form').jqm({
            trigger: '#blog_delete_show'
        });
        $('#add_friend_form').jqm({
            trigger: '#add_friend_show'
        });
        $('#window_upload_img').jqm();
        $('#userfield_form').jqm();
        $('#favourite-form-tags').jqm();
        $('#modal_write').jqm({
            trigger: '#modal_write_show'
        });
        $('#foto-resize').jqm({
            modal: true
        });
        $('#avatar-resize').jqm({
            modal: true,
            trigger: "#area-form-file-avatar"
        });
        $('#userfield_form').jqm({
            toTop: true
        });
        $('#photoset-upload-form').jqm({
            trigger: '#photoset-start-upload'
        });

        $('.js-registration-form-show').click(function() {
            if (Blocks.switchTab('registration', 'popup-login')) {
                $('#window_login_form').jqmShow();
            } else {
                window.location = aRouter.registration;
            }
            return false;
        });

        $('#window_blog_description').jqm({
            trigger: '#test-trigger'
        });

        $('.js-login-form-show').click(function() {
            if (Blocks.switchTab('login', 'popup-login')) {
                $('#window_login_form').jqmShow();
            } else {
                window.location = aRouter.login;
            }
            return false;
        });

        // Datepicker
        /**
         * TODO: навесить языки на datepicker
         */
        $('.date-picker').datepicker({
            dateFormat: 'dd.mm.yy',
            dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
            monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
            firstDay: 1
        });


        // Поиск по тегам
        $('.js-tag-search-form').submit(function() {
            window.location = aRouter['tag'] + encodeURIComponent($(this).find('.js-tag-search').val()) + '/';
            return false;
        });


        // Автокомплит
        Autocomplete.add($(".autocomplete-tags-sep"), aRouter['ajax'] + 'autocompleter/tag/', true);
        Autocomplete.add($(".autocomplete-tags"), aRouter['ajax'] + 'autocompleter/tag/', false);
        Autocomplete.add($(".autocomplete-users-sep"), aRouter['ajax'] + 'autocompleter/user/', true);
        Autocomplete.add($(".autocomplete-users"), aRouter['ajax'] + 'autocompleter/user/', false);


        // Всплывающие сообщения
        if (Registry.get('block_stream_show_tip')) {
            $('.js-title-comment, .js-title-topic').poshytip({
                className: 'infobox-yellow',
                alignTo: 'target',
                alignX: 'left',
                alignY: 'center',
                offsetX: 10,
                liveEvents: true,
                showTimeout: 500
            });
        }

        $('.js-title-talk').poshytip({
            className: 'infobox-yellow',
            alignTo: 'target',
            alignX: 'left',
            alignY: 'center',
            offsetX: 10,
            liveEvents: true,
            showTimeout: 500
        });

        $('.js-infobox-vote-topic').poshytip({
            content: function() {
                var id = $(this).attr('id').replace('vote_total_topic_', 'vote-info-topic-');
                return $('#' + id).html();
            },
            className: 'infobox-standart',
            alignTo: 'target',
            alignX: 'center',
            alignY: 'top',
            offsetX: 2,
            liveEvents: true,
            showTimeout: 100
        });

        $('.js-tip-help').poshytip({
            className: 'infobox-standart',
            alignTo: 'target',
            alignX: 'right',
            alignY: 'center',
            offsetX: 5,
            liveEvents: true,
            showTimeout: 500
        });

        $('.js-infobox').poshytip({
            className: 'infobox-standart',
            liveEvents: true,
            showTimeout: 300
        });

        // подсветка кода
        // prettyPrint();

        // инизиализация блоков
        Blocks.init('stream', {
            group_items: true,
            group_min: 3
        });
        Blocks.init('blogs');
        Blocks.initSwitch('tags');
        Blocks.initSwitch('upload-img');
        Blocks.initSwitch('favourite-topic-tags');
        Blocks.initSwitch('popup-login');

        // комментарии
        Comments.options.folding = false;
        Comments.init();

        // избранное
        Hook.add('ls_favourite_toggle_after', function(idTarget, objFavourite, type, params, result) {
            $('#fav_count_' + type + '_' + idTarget).text((result.iCount > 0) ? result.iCount : '');
        });

        /****************
         * TALK
         */

        // Добавляем или удаляем друга из списка получателей
        $('#friends input:checkbox').change(function() {
            Talk.toggleRecipient($('#' + $(this).attr('id') + '_label').text(), $(this).attr('checked'));
        });

        // Добавляем всех друзей в список получателей
        $('#friend_check_all').click(function() {
            $('#friends input:checkbox').each(function(index, item) {
                Talk.toggleRecipient($('#' + $(item).attr('id') + '_label').text(), true);
                $(item).attr('checked', true);
            });
            return false;
        });

        // Удаляем всех друзей из списка получателей
        $('#friend_uncheck_all').click(function() {
            $('#friends input:checkbox').each(function(index, item) {
                Talk.toggleRecipient($('#' + $(item).attr('id') + '_label').text(), false);
                $(item).attr('checked', false);
            });
            return false;
        });

        // Удаляем пользователя из черного списка
        $("#black_list_block").delegate("a.delete", "click", function() {
            Talk.removeFromBlackList(this);
            return false;
        });

        // Удаляем пользователя из переписки
        $("#speaker_list_block").delegate("a.delete", "click", function() {
            Talk.removeFromTalk(this, $('#talk_id').val());
            return false;
        });


        // Help-tags link
        $('.js-tags-help-link').click(function() {
            var target = Registry.get('tags-help-target-id');
            if (!target || !$('#' + target).length) {
                return false;
            }
            target = $('#' + target);
            if ($(this).data('insert')) {
                var s = $(this).data('insert');
            } else {
                var s = $(this).text();
            }
            $.markItUp({
                target: target,
                replaceWith: s
            });
            return false;
        });


        // Фикс бага с z-index у встроенных видео
        $("iframe").each(function() {
            var ifr_source = $(this).attr('src');

            if (ifr_source) {
                var wmode = "wmode=opaque";

                if (ifr_source.indexOf('?') != -1)
                    $(this).attr('src', ifr_source + '&' + wmode);
                else
                    $(this).attr('src', ifr_source + '?' + wmode);
            }
        });

        showFloatBlock($);




        // Хук конца инициализации javascript-составляющих шаблона
        Hook.run('ls_template_init_end', [], window);

        function spoiler(b) {
            if (b.style.display != "block") {
                jQuery(b).show(300);
                b.style.display = "block";
                $(b).find("img").each(function(k,v){v.src=v.getAttribute("data-src")})
                b.parentElement.getElementsByClassName("spoiler-title")[0].className = "spoiler-title spoiler-open";
            } else {
                jQuery(b).hide(300);
                b.parentElement.getElementsByClassName("spoiler-title")[0].className = "spoiler-title spoiler-close";

            }
        }
        console.log('now')

        function spoiler_click(event) {
            var event = event || window.event;
            if (event.button != 0) return;
            var target = event.target || event.srcElement;
            if (!target) return;
            if (target.tagName=="IMG") {
                if (target.id == "image-modal-img") {
                    return
                }
              $("#image-modal-img")[0].src = target.src
              $("#image-modal").css("display", "flex")
            }
            while (!target.classList.contains("spoiler-title")) {
                target = target.parentNode || target.parentElement;
                if (!target || target == document.body) return;
            }

            var parent = target.parentNode || target.parentElement;
            if (!parent || parent.lastElementChild == target) return true;
            var b = parent.querySelector(".spoiler-body");
            if (!b) return;
            spoiler(b);
            event.preventDefault ? event.preventDefault() : (event.returnValue = false);
            return false;
        }

        window.addEventListener("DOMContentLoaded", function() {
            document.body.addEventListener("click", spoiler_click);
        });

        var allNew = document.querySelectorAll('.spoiler-title');
        console.log(allNew)
        var idx = 0
        for (idx = 0; idx < allNew.length; idx++) {
            allNew[idx].className = "spoiler-title spoiler-close"
        }

        var despoil = function() {
            var allBody = document.querySelectorAll('.spoiler-body');
            idx = 0
            for (idx = 0; idx < allBody.length; idx++) {
                allBody[idx].style.display = "block"
            }
            var allNew = document.querySelectorAll('.spoiler-title');
            idx = 0
            for (idx = 0; idx < allNew.length; idx++) {
                allNew[idx].className = "spoiler-title spoiler-open"
            }
        }

        var spoil = function() {
            var allBody = document.querySelectorAll('.spoiler-body');
            idx = 0
            for (idx = 0; idx < allBody.length; idx++) {
                allBody[idx].style.display = "none"
            }
            var allNew = document.querySelectorAll('.spoiler-title');
            idx = 0
            for (idx = 0; idx < allNew.length; idx++) {
                allNew[idx].className = "spoiler-title spoiler-close"
            }
        }

        updateImgs()
    });
}
