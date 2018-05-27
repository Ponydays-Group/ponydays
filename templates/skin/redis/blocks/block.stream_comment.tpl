<ul class="item-list">
	{foreach from=$aComments item=oComment name="cmt"}
		{assign var="oUser" value=$oComment->getUser()}
		{assign var="oTopic" value=$oComment->getTarget()}
		{*{if $oTopic != null}*}
			{assign var="oBlog" value=$oTopic->getBlog()}
			{*{if $oBlog != null}*}

				<li class="js-title-comment">
					<a href="{$oUser->getUserWebPath()}"><img src="{$oComment->getUserAvatar()}" height="48" width="48" alt="avatar" class="avatar" /></a>

					<a href="{if $oConfig->GetValue('module.comment.nested_per_page')}{router page='comments'}{else}{$oTopic->getUrl()}#comment{/if}{$oComment->getId()}">{$oTopic->getTitle()|escape:'html'}</a>
					<a href="{$oBlog->getUrlFull()}">{$oBlog->getTitle()}</a>

					<p>
						<time datetime="{date_format date=$oComment->getDate() format='c'}">{date_format date=$oComment->getDate() hours_back="12" minutes_back="60" now="60" day="day H:i" format="j F Y, H:i"}</time> |
						{$oTopic->getCountComment()} {if $oTopic->getCountCommentNew()}<span>(+{$oTopic->getCountCommentNew()})</span>{/if} {$oTopic->getCountComment()|declension:$aLang.comment_declension:'russian'}
					</p>
				</li>
			{*{/if}*}
		{*{/if}*}
	{/foreach}
</ul>


<footer>
	<a href="{router page='comments'}">{$aLang.block_stream_comments_all}</a> | <a href="{router page='rss'}allcomments/">RSS</a>
</footer>
