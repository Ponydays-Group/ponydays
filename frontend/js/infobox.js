import Emitter from "./emitter"
import * as Msg from "./msg"
import $ from "jquery"
import * as Ajax from "./ajax"

/**
 * Всплывающие поп-апы
 */

/**
 * Шаблон процесс-бара
 */
export const sTemplateProcess = ["<div class=\"infobox-process\">process..",
    "</div>"].join("");
export const aLinks = [];
export const aOptDef = {
    hideOther: true,
    className: "infobox-standart",
    showOn: "none",
    alignTo: "target",
    alignX: "inner-left",
    alignY: "bottom",
    offsetX: -14,
    offsetY: 5,
    fade: false,
    slide: false,
    bgImageFrameSize: 10,
    showTimeout: 500,
    hideTimeout: 100,
    timeOnScreen: 0,
    liveEvents: false,
    allowTipHover: true,
    followCursor: false,
    slideOffset: 8,
    showAniDuration: 300,
    hideAniDuration: 300,
    refreshAniDuration: 200,
};

export function constructor() {
    $(document).click(function(e) {
        if(e.which == 1 && !$(e.target).data("isPoshytip")) {
            this.hideAll();
        }
    }.bind(this));
    $("body").on("click", ".js-infobox", function(e) {
        e.stopPropagation();
    });
}

export function show(oLink, sContent, aOpt) {
    aOpt = $.extend(true, {}, this.aOptDef, aOpt || {});

    if(aOpt.hideOther) {
        this.hideAll();
    }

    const $oLink = $(oLink);
    if($oLink.data("isPoshytip")) {
        $oLink.poshytip("update", sContent);
    } else {
        $oLink.on("click", function(e) {
            e.stopPropagation();
        });
        $oLink.poshytip({
            className: "js-infobox " + aOpt.className,
            content: sContent,
            showOn: aOpt.showOn,
            alignTo: aOpt.alignTo,
            alignX: aOpt.alignX,
            fade: aOpt.fade,
            slide: aOpt.slide,
            alignY: aOpt.alignY,
            offsetX: aOpt.offsetX,
            offsetY: aOpt.offsetY,
            bgImageFrameSize: aOpt.bgImageFrameSize,
            showTimeout: aOpt.showTimeout,
            hideTimeout: aOpt.hideTimeout,
            timeOnScreen: aOpt.timeOnScreen,
            liveEvents: aOpt.liveEvents,
            allowTipHover: aOpt.allowTipHover,
            followCursor: aOpt.followCursor,
            slideOffset: aOpt.slideOffset,
            showAniDuration: aOpt.showAniDuration,
            hideAniDuration: aOpt.hideAniDuration,
            refreshAniDuration: aOpt.refreshAniDuration,
        });
        $oLink.data("isPoshytip", 1);
        this.aLinks.push($oLink);
    }

    $oLink.poshytip("show");
}

export function hideAll() {
    $.each(this.aLinks, function(k, oLink) {
        this.hide(oLink);
    }.bind(this));
}

export function hide(oLink) {
    $(oLink).poshytip("hide");
    return false;
}

export function hideIfShow(oLink) {
    if($(oLink).data("poshytip") && $(oLink).data("poshytip").$tip.data("active")) {
        this.hide(oLink);
        return true;
    }
    return false;
}

export function showProcess(oLink, aOpt) {
    this.show(oLink, this.sTemplateProcess, aOpt);
}

export function showInfoBlog(oLink, iBlogId) {
    if(this.hideIfShow(oLink)) {
        return false;
    }

    this.showProcess(oLink);
    const url = "/ajax/infobox/info/blog/";
    const params = {iBlogId: iBlogId};
    "*showInfoBlogBefore*";
    "*/showInfoBlogBefore*";
    Ajax.ajax(url, params, function(result) {
        if(result.bStateError) {
            Msg.error(null, result.sMsg);
            this.hide(oLink);
        } else {
            this.show(oLink, result.sText);
            Emitter.emit("infobox_showinfoblog_after", [oLink, iBlogId, result]);
        }
    }.bind(this));
    return false;
}
