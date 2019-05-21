import * as Talk from "./talk"
import * as Ajax from "./ajax"
import * as Comments from "./comments"
import * as Registry from "./registry"
import * as Toolbar from "./toolbar"
import * as Autocomplete from "./autocomplete"
import * as Blocks from "./blocks"
import Emitter from "./emitter"
import hljs from "highlightjs"

import "./jquery/jquery.jqmodal"
import "jquery-ui-bundle/jquery-ui"
import "./jquery/jquery.poshytip"

function showFloatBlock($) {
    if(!$(".block-type-stream").length) {
        return;
    }
    $.browser.isMobileDevice = /android|webos|iphone|ipad|ipod|blackberry/i.test(navigator.userAgent.toLowerCase());
    if($.browser.isMobileDevice || $(window).width() < 1024 || $(window).height() < 500 || screen.width < 1024) {
        return;
    }
    let showFloat = false;
    const reinit = function() {
        const floatBlock = $(".block-type-stream");
        if($(window).width() < 1024 || $(window).height() < 500 || screen.width < 1024) {
            if(showFloat) {
                floatBlock.removeClass("stream-fixed");
                showFloat = false;
            }
            return;
        }
        const sidebar = $("#sidebar");
        const last_block = $($(".block:not(.hidden)")[$(".block:not(.hidden)").length - 1]);
        if(last_block.hasClass("block-type-stream")) {
            bottomPos = 0
        } else {
            var bottomPos = last_block.offset().top + last_block.outerHeight();
        }
        /*if(showFloat) {
            //bottomPos += floatBlock.outerHeight();
        }*/
        if(window.pageYOffset > bottomPos) {
            if(!showFloat) {
                floatBlock.addClass("stream-fixed");
                floatBlock.css("width", sidebar.width());
                //floatBlock.offset({left: Math.min($(window).width - 360, $(window).width / 2 + 1280/2 - 360)});
                floatBlock.animate({
                    opacity: 0,
                }, 0, function() {
                    floatBlock.animate({
                        opacity: 1,
                    }, 350)
                });
                showFloat = true;
            }
        } else {
            if(showFloat) {
                floatBlock.removeClass("stream-fixed");
                showFloat = false;
            }
        }
    };
    if($(window).width() > 1000)
        $(window).bind("scroll resize", reinit);

    reinit();
}

export function updateImgs(el = $(document)) {
    $(".spoiler-body img", el).each(
        function(k, v) {
            if(!v.getAttribute("data-src")) {
                // console.log(v.src, v)
                v.setAttribute("data-src", v.src);
                v.src = "#"
            }
        },
    )
}

export default function init() {
    $(document).ready(function($) {
        let shortcuts_shown = false
        $(".keyboard_shortcuts_trigger").click(function() {
            $("#comment-shortcuts-description").animate({"right": shortcuts_shown ? "-450px" : "74px"}, 200);
            shortcuts_shown = !shortcuts_shown
        });
        const el = $("#head_image")[0];
        if(localStorage.getItem("headCollapsed", 0) == null || localStorage.getItem("headCollapsed", 0) == "null") {
            if($(window).width() < 1500) {
                localStorage.setItem("headCollapsed", 1);
            }
        }

        function checkCollapse() {
            if(parseInt(localStorage.getItem("headCollapsed", 0))) {
                $(document.body).css("paddingTop", "0px");
                $("#head_image").css("height", "0px");
                $("#head_collaps i")[0].innerText = "keyboard_arrow_down";
                $("#rightbar").animate({paddingTop: "0px"}, 200);
            } else {
                $(document.body).css("paddingTop", "200px");
                $("#head_image").css("height", "200px");
                $("#head_collaps i")[0].innerText = "keyboard_arrow_up";
                $("#rightbar").animate({paddingTop: "200px"}, 200);
            }
        }

        function checkSidebarPadding() {
            $("#rightbar").css("paddingTop", el.getBoundingClientRect().bottom > 0 ? el.getBoundingClientRect().bottom + "px" : "0px");
        }

        function scrollBottom() {
            const body = $("html, body");
            body.stop();
            body.animate({
                scrollTop: $(document).height(),
            }, 150);
        }

        function scrollTop() {
            const body = $("html, body");
            body.stop();
            body.animate({
                scrollTop: 0,
            }, 150);
        }

        $("#scroll_down").click(scrollBottom);

        $("#scroll_up").click(scrollTop);

        if($("#rightbar").width() > 40)
            document.addEventListener("scroll", checkSidebarPadding);

        checkCollapse();
        $("#head_collaps").click(
            function() {
                parseInt(localStorage.getItem("headCollapsed")) ? localStorage.setItem("headCollapsed", 0) : localStorage.setItem("headCollapsed", 1);
                checkCollapse();
            });

        Emitter.emit("template_init_start", [], window);

        $("title").data("title", document.title);

        // updateImgs()
        $("#image-modal").click(function() {
            $("#image-modal").css("display", "none")
        });
        $("#image-modal-img").click(function() {
            $("#image-modal").css("display", "none")
        });

        $("html").removeClass("no-js");

        // window.resize_sidebar = function() {
        //     if ($("#content").offset().top == $("#sidebar").offset().top) {
        //         $("#sidebar").css("height", $("#wrapper").height() > $("#sidebar").height() ? $("#wrapper").height() : null)
        //     }
        // }
        // try {
        //     resize_sidebar()
        // } catch(err) {
        //     console.log(err)
        // }

        // Всплывающие окна
        $("#window_login_form").jqm();
        $("#blog_delete_form").jqm({
            trigger: "#blog_delete_show",
        });
        $("#add_friend_form").jqm({
            trigger: "#add_friend_show",
        });
        $("#window_upload_img").jqm({
            onShow: function(hash) {
                hash.w.show();
                if($("#img_file").is(":visible")) {
                    setTimeout(function() {
                        $("#img_file").focus();
                    }, 100);
                } else {
                    setTimeout(function() {
                        $("#img_url").focus();
                    }, 100);
                }
            },
            onHide: function(hash) {
                hash.w.hide() && hash.o && hash.o.remove();
                if($("#reply").is(":visible")) {
                    $("#reply").focus();
                } else {
                    $("#topic_text").focus();
                }
            },
        });
        $("#userfield_form").jqm();
        $("#quotes_form").jqm();
        $("#favourite-form-tags").jqm();
        $("#modal_write").jqm({
            trigger: "#modal_write_show",
        });
        $("#foto-resize").jqm({
            modal: true,
        });
        $("#avatar-resize").jqm({
            modal: true,
            trigger: "#area-form-file-avatar",
        });
        $("#userfield_form").jqm({
            toTop: true,
        });
        $("#photoset-upload-form").jqm({
            trigger: "#photoset-start-upload",
        });

        $(".js-registration-form-show").click(function() {
            if(Blocks.switchTab("registration", "popup-login")) {
                $("#window_login_form").jqmShow();
            } else {
                window.location = aRouter.registration;
            }
            return false;
        });

        $("#window_blog_description").jqm({
            trigger: "#test-trigger",
        });

        $(".js-login-form-show").click(function() {
            if(Blocks.switchTab("login", "popup-login")) {
                $("#window_login_form").jqmShow();
            } else {
                window.location = aRouter.login;
            }
            return false;
        });

        // Datepicker
        /**
         * TODO: навесить языки на datepicker
         */
        $(".date-picker").datepicker({
            dateFormat: "dd.mm.yy",
            dayNamesMin: ["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
            monthNames: ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
            firstDay: 1,
        });


        // Поиск по тегам
        $(".js-tag-search-form").submit(function() {
            window.location = aRouter["tag"] + encodeURIComponent($(this).find(".js-tag-search").val()) + "/";
            return false;
        });


        // Автокомплит
        Autocomplete.add($(".autocomplete-tags-sep"), aRouter["ajax"] + "autocompleter/tag/", true);
        Autocomplete.add($(".autocomplete-tags"), aRouter["ajax"] + "autocompleter/tag/", false);
        Autocomplete.add($(".autocomplete-users-sep"), aRouter["ajax"] + "autocompleter/user/", true);
        Autocomplete.add($(".autocomplete-users"), aRouter["ajax"] + "autocompleter/user/", false);


        // Всплывающие сообщения
        // if (Registry.get('block_stream_show_tip')) {
        //     $('.js-title-comment, .js-title-topic').poshytip({
        //         className: 'infobox-yellow',
        //         alignTo: 'target',
        //         alignX: 'left',
        //         alignY: 'center',
        //         offsetX: 10,
        //         liveEvents: true,
        //         showTimeout: 500
        //     });
        // }

        $(".js-title-talk").poshytip({
            className: "infobox-yellow",
            alignTo: "target",
            alignX: "left",
            alignY: "center",
            offsetX: 10,
            liveEvents: true,
            showTimeout: 500,
        });

        $(".js-infobox-vote-topic").poshytip({
            content: function() {
                const id = $(this).attr("id").replace("vote_total_topic_", "vote-info-topic-");
                return $("#" + id).html();
            },
            className: "infobox-standart",
            alignTo: "target",
            alignX: "center",
            alignY: "top",
            offsetX: 2,
            liveEvents: true,
            showTimeout: 100,
        });

        $(".js-tip-help").poshytip({
            className: "infobox-standart",
            alignTo: "target",
            alignX: "right",
            alignY: "center",
            offsetX: 5,
            liveEvents: true,
            showTimeout: 500,
        });

        $(".js-infobox").poshytip({
            className: "infobox-standart",
            liveEvents: true,
            showTimeout: 300,
        });

        // подсветка кода
        // prettyPrint();

        // инизиализация блоков
        Blocks.init("stream", {
            group_items: true,
            group_min: 3,
        });
        Blocks.init("blogs");
        Blocks.initQuotes();
        Blocks.initSwitch("tags");
        Blocks.initSwitch("upload-img");
        Blocks.initSwitch("favourite-topic-tags");
        Blocks.initSwitch("popup-login");

        // комментарии
        Comments.options.folding = false;
        Comments.init();

        /****************
         * TALK
         */

        // Добавляем или удаляем друга из списка получателей
        $("#friends input:checkbox").change(function() {
            Talk.toggleRecipient($("#" + $(this).attr("id") + "_label").text(), this.checked);
        });

        // Добавляем всех друзей в список получателей
        $("#friend_check_all").click(function() {
            $("#friends input:checkbox").each(function(index, item) {
                Talk.toggleRecipient($("#" + $(item).attr("id") + "_label").text(), true);
                item.checked = true;
            });
            return false;
        });

        // Удаляем всех друзей из списка получателей
        $("#friend_uncheck_all").click(function() {
            $("#friends input:checkbox").each(function(index, item) {
                Talk.toggleRecipient($("#" + $(item).attr("id") + "_label").text(), false);
                item.checked = false;
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
            Talk.removeFromTalk(this, $("#talk_id").val());
            return false;
        });

        // Принимаем приглашение в переписку переписки
        $("#accept_invite_talk_back").delegate("a.delete", "click", function() {
            Talk.acceptInviteBackToTalk(this);
            return false;
        });


        // Help-tags link
        $(".js-tags-help-link").click(function() {
            let target = Registry.get("tags-help-target-id");
            if(!target || !$("#" + target).length) {
                return false;
            }
            target = $("#" + target);
            let s;
            if($(this).data("insert")) {
                s = $(this).data("insert");
            } else {
                s = $(this).text();
            }
            $.markItUp({
                target: target,
                replaceWith: s,
            });
            return false;
        });


        // Фикс бага с z-index у встроенных видео
        $("iframe").each(function() {
            const ifr_source = $(this).attr("src");

            if(ifr_source) {
                const wmode = "wmode=opaque";

                if(ifr_source.indexOf("?") != -1)
                    $(this).attr("src", ifr_source + "&" + wmode);
                else
                    $(this).attr("src", ifr_source + "?" + wmode);
            }
        });

        showFloatBlock($);

        Emitter.emit("template_init_end", [], window);

        window.closeSpoiler = function(b) {
            $(b).hide(300);
            if(!b.parentElement.getElementsByClassName("spoiler-title").length) {
                return;
            }
            b.parentElement.getElementsByClassName("spoiler-title")[0].className = "spoiler-title spoiler-close";
        };

        window.openSpoiler = function(b) {
            $(b).show(300);
            b.style.display = "block";
            $(b).find("img, iframe").each(function(k, v) {
                if(v.getAttribute("data-src")) {
                    v.src = v.getAttribute("data-src");
                }
            });
            if(!b.parentElement.getElementsByClassName("spoiler-title").length) {
                return;
            }
            b.parentElement.getElementsByClassName("spoiler-title")[0].className = "spoiler-title spoiler-open";
        };

        window.spoiler = function(b) {
            if(b.style.display != "block") openSpoiler(b); else closeSpoiler(b);
        };

        async function click(event) {
            var event = event || window.event;
            if(event.button != 0) return;
            let target = event.target || event.srcElement;
            if(!target) return;
            let parent = target.parentNode || target.parentElement;

            if(target.tagName == "IMG" && !parent.classList.contains("spoiler-title") && $(".text img").index(target) > (-1)) {
                if(target.id == "image-modal-img") {
                    return;
                }
                const modal_img_sel = $("#image-modal-img");
                modal_img_sel[0].src = "";
                modal_img_sel[0].src = target.src;
                $("#image-modal").css("display", "flex");
            }

            while(target.classList && !target.classList.contains("spoiler-title")) {
                target = target.parentNode || target.parentElement;
                if(!target || target == document.body) return;
            }

            parent = target.parentNode || target.parentElement;
            if(!parent || parent.lastElementChild == target) return true;
            var b = parent.querySelector(".spoiler-body");
            if(!b) return;
            spoiler(b);
            event.preventDefault ? event.preventDefault() : (event.returnValue = false);
            return false;
        }

        window.addEventListener("DOMContentLoaded", function() {
            document.body.addEventListener("click", click);
        });

        const allNew = document.querySelectorAll(".spoiler-title");
        let idx = 0;
        for(idx = 0; idx < allNew.length; idx++) {
            allNew[idx].className = "spoiler-title spoiler-close";
        }

        window.spoilers_closed = true;

        window.despoil = function() {
            $(".spoiler-body").each(function(k, v) {
                v.style.display = window.spoilers_closed ? "block" : "none";
                if(window.spoilers_closed) {
                    $(v).find("img, iframe").each(function(k, vv) {
                        if(vv.getAttribute("data-src")) {
                            vv.src = vv.getAttribute("data-src");
                        }
                    })
                }
            });
            window.spoilers_closed = !window.spoilers_closed;
        }.bind(this);


        window.widemode = function() {
            const content = $("#content");
            const sidebar = $("#sidebar");

            if(content.hasClass("col-md-9")) {
                sidebar.removeClass("col-md-3").addClass("col-md-0").css("display", "none");
                content.removeClass("col-md-9").addClass("col-md-12");
            } else {
                sidebar.removeClass("col-md-0").addClass("col-md-3").css("display", "block");
                content.removeClass("col-md-12").addClass("col-md-9");
            }

            Emitter.emit("do_comments_calc_nesting");
        }.bind(this);

        updateImgs();

        if(getCookie("SiteStyle") == "Dark") {
            $("#change_theme").css("transform", "rotate(180deg)");
        }

        $(".topic-more").click(function(e) {
            const el = $(e.target).parent().find(".topic-dropdown");
            el.toggleClass("active");
            if(el.hasClass("active")) {
                $(document).on("click", function(e1) {
                    if($(e1.target).parents(".topic-more").length) return;
                    el.toggleClass("active")
                    e1.stopPropagation()
                    $(document).off("click")
                }.bind(this))
            }
        })
    });
}
