import * as Msg from "./msg"
import Emitter from "./emitter"
import $ from "jquery"
import * as Ajax from "./ajax"

/**
 * Голосование
 */

/**
 * Опции
 */
export const options = {
    classes: {
        vote: "vote",
        voted: "voted",
        plus: "voted-up",
        minus: "voted-down",
        vote_count: "vote-count",
        positive: "vote-count-positive",
        negative: "vote-count-negative",
        mixed: "vote-count-mixed",
        voted_zero: "voted-zero",
        zero: "vote-count-zero",
        not_voted: "not-voted",
        hidden: "action-hidden",
    },
    prefix_area: "vote_area_",
    prefix_total: "vote_total_",
    prefix_count: "vote_count_",

    type: {
        comment: {
            url: "/ajax/vote/comment/",
            targetName: "idComment",
        },
        topic: {
            url: "/ajax/vote/topic/",
            targetName: "idTopic",
        },
        blog: {
            url: "/ajax/vote/blog/",
            targetName: "idBlog",
        },
        user: {
            url: "/ajax/vote/user/",
            targetName: "idUser",
        },
    },
};

export function vote(idTarget, objVote, value, type) {
    if(!this.options.type[type]) return false;

    objVote = $(objVote);
    const params = {};
    params["value"] = value;
    params[this.options.type[type].targetName] = idTarget;

    Emitter.emit("vote_vote_before");
    Ajax.ajax(this.options.type[type].url, params, function(result) {
        const args = [idTarget, objVote, value, type, result];
        this.onVote.apply(this, args);
    }.bind(this));
    return false;
}

export function onVote(idTarget, objVote, value, type, result) {
    if(result.bStateError) {
        Msg.error(null, result.sMsg);
    } else {
        Msg.notice(null, result.sMsg);

        var divVoting = $("#" + this.options.prefix_area + type + "_" + idTarget);

        divVoting.addClass(this.options.classes.voted);
        if(value > 0) {
            if(divVoting.hasClass(this.options.classes.plus)) {
                divVoting.removeClass(this.options.classes.plus);
            } else {
                divVoting.addClass(this.options.classes.plus);
            }
            divVoting.removeClass(this.options.classes.minus);
            divVoting.removeClass(this.options.classes.hidden);
        }
        if(value < 0) {
            if(divVoting.hasClass(this.options.classes.minus)) {
                divVoting.removeClass(this.options.classes.minus);
            } else {
                divVoting.addClass(this.options.classes.minus);
            }
            divVoting.removeClass(this.options.classes.plus);
            divVoting.removeClass(this.options.classes.hidden);
        }
        if(value == 0) {
            //divVoting.addClass(this.options.classes.hidden);
            divVoting.removeClass(this.options.classes.hidden);
            divVoting.addClass(this.options.classes.voted_zero);
        }

        const divTotal = $("#" + this.options.prefix_total + type + "_" + idTarget);
        const divCount = $("#" + this.options.prefix_count + type + "_" + idTarget);

        if(divCount.length > 0 && result.iCountVote) {
            divCount.text(parseInt(result.iCountVote));
        }

        result.iRating = parseFloat(result.iRating);
        result.iCountVote = parseInt(result.iCountVote);

        divVoting.removeClass(this.options.classes.negative);
        divVoting.removeClass(this.options.classes.positive);
        divVoting.removeClass(this.options.classes.mixed);
        divVoting.removeClass(this.options.classes.not_voted);
        divVoting.removeClass(this.options.classes.zero);

        if(result.iRating > 0) {
            divVoting.addClass(this.options.classes.positive);
            divTotal.text("+" + result.iRating);
        } else if(result.iRating < 0) {
            divVoting.addClass(this.options.classes.negative);
            divTotal.text(result.iRating);
        } else if(result.iRating == 0 && result.iCountVote > 0) {
            divVoting.addClass(this.options.classes.mixed);
            divTotal.text(result.iRating);
        } else if(result.iRating == 0) {
            divVoting.addClass(this.options.classes.zero);
            divTotal.text(0);
        }

        if(result.iCountVote > 0) divTotal[0].dataset.count = result.iCountVote;

        const method = "onVote" + ls.tools.ucfirst(type);
        if($.type(this[method]) == "function") {
            this[method].apply(this, [idTarget, objVote, value, type, result]);
        }

    }

    $(this).trigger("vote", [idTarget, objVote, value, type, result]);
}


export function onVoteUser(idTarget, objVote, value, type, result) {
    $("#user_skill_" + idTarget).text(result.iSkill);
}

export function getVotes(targetId, targetType, el, toggleControl) {
    const perm = localStorage.getItem("no_show_vote");
    if(perm != null && parseInt(perm))
        return;
    const params = {};
    if(toggleControl && el.classList.contains("toggled")) {
        el.classList.remove("toggled");
        return;
    }
    params["targetId"] = targetId;
    params["targetType"] = targetType;
    const url = "/ajax/get-object-votes";
    Ajax.ajax(url, params, this.onGetVotes.bind({
        "orig": this,
        "control": el,
        "targetType": targetType,
        "toggleControl": toggleControl,
    }));
    el.classList.add("in-progress");
    return false;
}

export function __makeProfileLink(path, data) {
    const el = document.createElement("a");
    if(path != null && data.name != null) {
        el.href = "/profile/" + path;
        el.target = "_blank";
        el.className = "ls-user has-avatar";
        const avatar = document.createElement("img");
        avatar.src = data.avatar;
        el.appendChild(avatar);
        el.appendChild(document.createTextNode(data.name));
    } else {
        el.href = "javascript://";
        el.className = "ls-user undefined";
        el.appendChild(document.createTextNode("—"));
    }
    return el;
}

export function onGetVotes(result) {
    if(result.bStateError) {
        Msg.error(null, result.sMsg);
    } else {
        let voteSum = 0;
        if(result.aVotes.length > 0) {
            var vl = document.createElement("div");
            vl.className = "vote-list";
            for(var i = 0; i < result.aVotes.length; i++) {
                const vote = result.aVotes[i];
                voteSum += vote.value;
                const line = document.createElement("div");
                line.className = "vote-list-item";
                var profileLink = __makeProfileLink(vote.voterName, {
                    name: vote.voterName,
                    avatar: vote.voterAvatar,
                });
                profileLink.classList.add("vote-list-item-component");
                line.appendChild(profileLink);

                const time = document.createElement("time");
                time.datetime = vote.date;
                const date = new Date(Date.parse(vote.date));
                const now = new Date();
                time.appendChild(document.createTextNode((
                    date.getDate() != now.getDate() ||
                    date.getMonth() != now.getMonth() ||
                    date.getFullYear() != now.getFullYear()
                ) ? date.toLocaleString() : date.toLocaleTimeString()));
                time.className = "vote-list-item-component";
                line.appendChild(time);

                const voteValue = document.createElement("span");
                voteValue.dataset.value = vote.value == 0 ? "0" : (vote.value > 0 ? "+" : "−") + Math.abs(vote.value).toString();
                voteValue.className = "vote-list-item-component vote";
                line.appendChild(voteValue);

                vl.appendChild(line);
            }
            const vl_box = document.createElement("div");
            vl_box.className = "vote-list-box hidden for-" + this.targetType;
            const vl_closeButton = document.createElement("a");
            vl_closeButton.className = "close-button";
            vl_closeButton.href = "javascript://";
            vl_closeButton.textContent = "Закрыть"; // ! locale-specific
            const vl_wrapper = document.createElement("div");
            vl_wrapper.className = "vote-list-wrapper";
            vl_wrapper.appendChild(vl);
            vl_box.appendChild(vl_closeButton);
            vl_box.appendChild(vl_wrapper);
            switch(this.targetType) {
            case "comment":
                if(this.control.parentNode.parentNode.classList.contains("comment-actions")) {
                    vl_box.classList.add("for-tree-comment");
                    this.control.parentNode.insertBefore(vl_box, this.control);
                } else {
                    vl_box.classList.add("for-lone-comment");
                    this.control.parentNode.parentNode.parentNode.insertBefore(vl_box, this.control.parentNode.parentNode);
                }
                break;
            case "topic":
                this.control.parentNode.parentNode.parentNode.insertBefore(vl_box, this.control.parentNode.parentNode);
                break;
            }
            setTimeout(DOMTokenList.prototype.remove.bind(vl_box.classList), 10, "hidden");
            /*
            if(vl_box.scrollHeight > vl_box.clientHeight) {
                vl_box.style.width = (vl_box.clientWidth + 24) + "px";
                vl_box.style.overflowY = "scroll";
            }
            */
            if(this.toggleControl) {
                this.control.classList.add("toggled");
            }

            const context = {
                "target": vl_box,
                "eventTarget": window,
                "control": this.control,
                "toggleControl": this.toggleControl,
            };
            context.callback = this.orig.onVotesListLeaved.bind(context);
            context.eventTarget.addEventListener("click", context.callback);
        }

        if(parseInt(this.control.dataset.count) != result.aVotes.length) {
            let classZero = this.targetType == "comment" ? this.orig.options.classes.hidden : this.orig.options.classes.zero;
            this.control.parentNode.classList.remove(this.orig.options.classes.negative);
            this.control.parentNode.classList.remove(this.orig.options.classes.positive);
            this.control.parentNode.classList.remove(this.orig.options.classes.mixed);
            this.control.parentNode.classList.remove(classZero);
            let textContent = voteSum.toString();
            if(voteSum > 0) {
                textContent = "+" + textContent;
                this.control.parentNode.classList.add(this.orig.options.classes.positive);
            } else if(voteSum < 0) {
                this.control.parentNode.classList.add(this.orig.options.classes.negative);
            } else if(result.aVotes.length > 0) {
                this.control.parentNode.classList.add(this.orig.options.classes.mixed);
            } else {
                this.control.parentNode.classList.add(classZero);
            }
            this.control.textContent = textContent;
            this.control.dataset.count = result.aVotes.length.toString();
        }
    }
    this.control.classList.remove("in-progress");
}

export function onVotesListLeaved(e) {
    const eventTarget = e.target;
    if(
        eventTarget.classList.contains("close-button")
        ||
        (
            (
                (
                    this.target != eventTarget
                    &&
                    eventTarget.tagName != "A"
                )
                ||
                /*Костыль для совместимости с <a> в качестве счётчика голосов топика:*/
                eventTarget.classList.contains("vote-count")
                ||
                /*Костыль для совместимости с <a> в качестве кнопок действий с href="#":*/
                (eventTarget.attributes.href && eventTarget.attributes.href.value == "#")
            )
            &&
            !this.target.contains(eventTarget)
        )
    ) {
        this.target.classList.add("hidden");
        if(this.toggleControl) {
            this.control.classList.remove("toggled");
        }
        //setTimeout(Element.prototype.remove.bind(this.target), 500);
        setTimeout(Node.prototype.removeChild.bind(this.target.parentNode), 500, this.target);
        this.eventTarget.removeEventListener(e.type, this.callback);
    }
}
