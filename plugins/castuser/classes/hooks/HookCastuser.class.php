<?php

class PluginCastuser_HookCastuser extends Hook
{

    /**
     * Register hooks
     */
    public function RegisterHook()
    {
        $this->AddHook('topic_add_after', 'NotifyCastedUserTopic');
        $this->AddHook('topic_edit_after', 'NotifyCastedUserTopic');
        $this->AddHook('comment_add_after', 'NotifyCastedUserComment');
        
    }

    public function NotifyCastedUserTopic($aParams)
    {        
    	$oTopic = $aParams['oTopic'];
    	if ($oTopic->getPublish()==1 ){    		
    		$oTopic->setBlog($this->Blog_GetBlogById($oTopic->getBlogId()));
    		$this->PluginCastuser_Cast_sendCastNotify('topic',$oTopic,null,$oTopic->getTextSource());
    	}
    }

    
    public function NotifyCastedUserComment($aParams)
    {        
    	$oTarget = $aParams['oCommentNew'];
    	$oParrentTarget = $aParams['oTopic'];
    	$this->PluginCastuser_Cast_sendCastNotify('comment',$oTarget,$oParrentTarget,$oTarget->getText());
    }    
    
}
