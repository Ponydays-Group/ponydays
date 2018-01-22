        {assign var="oUser" value=$oComment->getUser()}
        {*{assign var="oTarget" value=$oComment->getTarget()}*}



        {*<div class="comment-path">*}
            {*<a href="{$oTarget->getUrl()}">{$oTarget->getTitle()|escape:'html'}</a>*}
            {*<a href="{$oTarget->getUrl()}#comments">({$oTarget->getCountComment()})</a>*}
        {*</div>*}


        <section class="comment {if $oComment->getDelete()}comment-deleted{/if}">
            <a href="{$oUser->getUserWebPath()}"><img src="{$oUser->getProfileAvatarPath(48)}" alt="avatar" class="comment-avatar" /></a>


            <ul class="comment-info clearfix">
                <li class="comment-author"><a href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a></li>
                <li class="comment-date">
                    <a href="{if $oConfig->GetValue('module.comment.nested_per_page')}{router page='comments'}{else}{$oTarget->getUrl()}#comment{/if}{$oComment->getId()}" class="link-dotted" title="{$aLang.comment_url_notice}">
                        <time datetime="{date_format date=$oComment->getDate() format='c'}">{date_format date=$oComment->getDate() hours_back="12" minutes_back="60" now="60" day="day H:i" format="j F Y, H:i"}</time>
                    </a>
                </li>
                {if $oUserCurrent and !$bNoCommentFavourites}
                    <li class="comment-favourite">
                        <div onclick="return ls.favourite.toggle({$oComment->getId()},this,'comment');" class="fa fa-heart-o favourite{if $oComment->getIsFavourite()} active{/if}"></div>
                        <span class="favourite-count" id="fav_count_comment_{$oComment->getId()}">{if $oComment->getCountFavourite() > 0}{$oComment->getCountFavourite()}{/if}</span>
                    </li>
                {/if}
                {if $oComment->getPid()}
                    <li>
                    <a href="{$oTarget->GetUrl()}#comment{$oComment->getPid()}" title="Перейти к родительскому комментарию">
                        ↑
                    </a></li>
                {/if}
                <li id="vote_area_comment_{$oComment->getId()}" class="vote
																		{if $oComment->getRating() > 0}
																			vote-count-positive
																		{elseif $oComment->getRating() < 0}
																			vote-count-negative
																		{elseif $oComment->getCountVote() > 0}
																			vote-count-mixed
																		{else}
																			vote-count-zero
																		{/if}">
                    <span class="vote-count" onclick="ls.vote.getVotes({$oComment->getId()},'comment',this); return false;" data-count="{$oComment->getCountVote()}" id="vote_total_comment_{$oComment->getId()}">{$oComment->getRating()}</span>
                </li>
            </ul>


            <div id="comment_content_id_{$oComment->getId()}" class="comment-content text {if $oComment->getDelete()}hided{/if}">
                {if $oComment->getDelete()}<div class="delete-reason">{if $oComment->getDeleteReason()}{$oComment->getDeleteReason()}{else}Нет причины удаления{/if}</div>{/if}
                {if $oUserCurrent && ($oComment->getDelete() && ($bCanDelete || $oUserCurrent->getId()==$oUser->getId()))}
                    <a href="#" onclick="ls.comments.showHiddenComment({$oComment->getId()}); return false;">Раскрыть комментарий</a>
                {/if}
                {if !$oComment->getDelete()}
                    {$oComment->getText()}
                {/if}
            </div>
        </section>