import $ from 'jquery'
import * as Ajax from './ajax'

/**
 * Голосование
 */

/**
 * Опции
 */
export let options = {
    classes: {
        voted: 'voted',
        plus: 'voted-up',
        minus: 'voted-down',
        positive: 'vote-count-positive',
        negative: 'vote-count-negative',
        mixed: 'vote-count-mixed',
        voted_zero: 'voted-zero',
        zero: 'vote-count-zero',
        not_voted: 'not-voted',
        hidden: 'action-hidden',
    },
    prefix_area: 'vote_area_',
    prefix_total: 'vote_total_',
    prefix_count: 'vote_count_',

    type: {
        comment: {
            url: aRouter['ajax'] + 'vote/comment/',
            targetName: 'idComment'
        },
        topic: {
            url: aRouter['ajax'] + 'vote/topic/',
            targetName: 'idTopic'
        },
        blog: {
            url: aRouter['ajax'] + 'vote/blog/',
            targetName: 'idBlog'
        },
        user: {
            url: aRouter['ajax'] + 'vote/user/',
            targetName: 'idUser'
        }
    }
};

export function vote(idTarget, objVote, value, type) {
    if (!this.options.type[type]) return false;

    objVote = $(objVote);
    var params = {};
    params['value'] = value;
    params[this.options.type[type].targetName] = idTarget;

    ls.hook.marker('voteBefore');
    Ajax.ajax(this.options.type[type].url, params, function (result) {
        var args = [idTarget, objVote, value, type, result];
        this.onVote.apply(this, args);
    }.bind(this));
    return false;
}

export function onVote(idTarget, objVote, value, type, result) {
    if (result.bStateError) {
        ls.msg.error(null, result.sMsg);
    } else {
        ls.msg.notice(null, result.sMsg);

        var divVoting = $('#' + this.options.prefix_area + type + '_' + idTarget);

        divVoting.addClass(this.options.classes.voted);

        if (value > 0) {
            divVoting.addClass(this.options.classes.plus);
            divVoting.removeClass(this.options.classes.minus);
            divVoting.removeClass(this.options.classes.hidden);
        }
        if (value < 0) {
            divVoting.addClass(this.options.classes.minus);
            divVoting.removeClass(this.options.classes.plus);
            divVoting.removeClass(this.options.classes.hidden);
        }
        if (value == 0) {
            divVoting.addCLass(this.options.classes.hidden);
            divVoting.addClass(this.options.classes.voted_zero);
        }

        var divTotal = $('#' + this.options.prefix_total + type + '_' + idTarget);
        var divCount = $('#' + this.options.prefix_count + type + '_' + idTarget);

        if (divCount.length > 0 && result.iCountVote) {
            divCount.text(parseInt(result.iCountVote));
        }

        result.iRating = parseFloat(result.iRating);
        result.iCountVote = parseInt(result.iCountVote);

        divVoting.removeClass(this.options.classes.negative);
        divVoting.removeClass(this.options.classes.positive);
        divVoting.removeClass(this.options.classes.mixed);
        divVoting.removeClass(this.options.classes.not_voted);
        divVoting.removeClass(this.options.classes.zero);

        if (result.iRating > 0) {
            divVoting.addClass(this.options.classes.positive);
            divTotal.text('+' + result.iRating);
        } else if (result.iRating < 0) {
            divVoting.addClass(this.options.classes.negative);
            divTotal.text(result.iRating);
        } else if (result.iRating == 0 && result.iCountVote > 0) {
            divVoting.addClass(this.options.classes.mixed);
            divTotal.text(result.iRating);
        } else if (result.iRating == 0) {
            divVoting.addClass(this.options.classes.zero);
            divTotal.text(0);
        }

        if (result.iCountVote > 0) divTotal[0].dataset.count = result.iCountVote;

        var method = 'onVote' + ls.tools.ucfirst(type);
        if ($.type(this[method]) == 'function') {
            this[method].apply(this, [idTarget, objVote, value, type, result]);
        }

    }

    $(this).trigger('vote', [idTarget, objVote, value, type, result]);
}


export function onVoteUser(idTarget, objVote, value, type, result) {
    $('#user_skill_' + idTarget).text(result.iSkill);
}

export function getVotes(targetId, targetType, el) {
    var params = {};
    params['targetId'] = targetId;
    params['targetType'] = targetType;
    var url = aRouter['ajax'] + 'get-object-votes';
    Ajax.ajax(url, params, this.onGetVotes.bind({"orig": this, "control": el}));
    el.dataset.queryState = "query";
    return false;
}

export function __makeProfileLink(path, data) {
    var el = document.createElement("a");
    if (path != null && data.name != null) {
        el.href = "/profile/" + path;
        el.target = "_blank";
        el.className = "ls-user has-avatar";
        var avatar = document.createElement("img");
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
    if (result.bStateError) {
        ls.msg.error(null, result.sMsg);
    } else {
        var voteSum = 0;
        if (result.aVotes.length > 0) {
            var vl = document.createElement("div");
            vl.className = "vote-list";
            for (var i = 0; i < result.aVotes.length; i++) {
                var vote = result.aVotes[i];
                voteSum += vote.value;
                var line = document.createElement("div");
                var profileLink = __makeProfileLink(vote.voterName, {
                    name: vote.voterName,
                    avatar: vote.voterAvatar
                });
                line.appendChild(profileLink);

                var time = document.createElement("time");
                time.datetime = vote.date;
                var date = new Date(Date.parse(vote.date));
                var now = new Date();
                time.appendChild(document.createTextNode((
                    date.getDate() != now.getDate() ||
                    date.getMonth() != now.getMonth() ||
                    date.getFullYear() != now.getFullYear()
                ) ? date.toLocaleString() : date.toLocaleTimeString()));
                line.appendChild(time);

                var voteValue = document.createElement("span");
                voteValue.dataset.value = vote.value == 0 ? "0" : (vote.value > 0 ? "+" : "−") + Math.abs(vote.value).toString();
                voteValue.className = "vote";
                line.appendChild(voteValue);

                vl.appendChild(line);
            }
            var vl_wrapper = document.createElement("div");
            vl_wrapper.className = "vote-list-wrapper hidden";
            vl_wrapper.appendChild(vl);
            if (this.control.parentNode.parentNode.classList.contains("comment-actions")) this.control.parentNode.insertBefore(vl_wrapper, this.control.parentNode.firstChild);
            else this.control.parentNode.parentNode.parentNode.insertBefore(vl_wrapper, this.control.parentNode.parentNode.nextSibling);
            setTimeout(DOMTokenList.prototype.remove.bind(vl_wrapper.classList), 10, "hidden");

            var context = {
                "target": vl_wrapper,
                "eventTarget": window
            };
            context.callback = this.orig.onVotesListLeaved.bind(context);
            context.eventTarget.addEventListener("click", context.callback);
        }

        if (parseInt(this.control.dataset.count) != result.aVotes.length) {
            this.control.parentNode.classList.remove(this.orig.options.classes.negative);
            this.control.parentNode.classList.remove(this.orig.options.classes.positive);
            this.control.parentNode.classList.remove(this.orig.options.classes.mixed);
            if (voteSum > 0) {
                this.control.textContent = "+" + voteSum.toString();
                this.control.parentNode.classList.add(this.orig.options.classes.positive);
            } else {
                this.control.textContent = voteSum.toString();
                if (voteSum < 0) this.control.parentNode.classList.add(this.orig.options.classes.negative);
                else this.control.parentNode.classList.add(this.orig.options.classes.mixed);
            }
            this.control.dataset.count = result.aVotes.length.toString();
        }
    }
    delete this.control.dataset.queryState;
}

export function onVotesListLeaved(e) {
    if (this.target != e.target && (
            e.target.tagName != "A" && !document.getElementsByClassName("nav-userbar")[0].contains(e.target) && !this.target.contains(e.target)
        )) {
        this.target.classList.add("hidden");
        //setTimeout(Element.prototype.remove.bind(this.target), 500);
        setTimeout(Node.prototype.removeChild.bind(this.target.parentNode), 500, this.target);
        this.eventTarget.removeEventListener(e.type, this.callback);
    }
}
