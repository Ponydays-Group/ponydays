{include file='header.tpl' menu='blog'}

{include file='topic.tpl'}
{include 
	file='comment_tree.tpl' 	
	iTargetId=$oTopic->getId()
	iAuthorId=$oTopic->getUserId()
	sAuthorNotice=$aLang.topic_author
	sTargetType='topic'
	iCountComment=$oTopic->getCountComment()
	sDateReadLast=$oTopic->getDateRead()
	bAllowNewComment=$oTopic->getForbidComment()
	sNoticeNotAllow=$aLang.topic_comment_notallow
	sNoticeCommentAdd=$aLang.topic_comment_add
	bAllowSubscribe=true
	oSubscribeComment=$oTopic->getSubscribeNewComment()
	aPagingCmt=$aPagingCmt
	bEnableCommentsVoteInfo=$E->make(App\Modules\ModuleACL::class)->CheckSimpleAccessLevel($oConfig->Get('acl.vote_list.comment.ne_enable_level'), $oUserCurrent, $oTopic, 'topic')
	}

{include file='footer.tpl'}