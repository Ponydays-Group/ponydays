<?php

namespace App\Actions\Ajax;

use App\Modules\ModuleComment;
use App\Modules\ModuleTopic;
use Engine\Config;
use Engine\Modules\ModuleLang;
use Engine\Result\View\AjaxView;
use Engine\Result\View\HtmlView;
use Engine\Routing\Controller;

class ActionAjaxStream extends Controller
{
    /**
     * Обработка получения последних комментов
     * Используется в блоке "Прямой эфир"
     *
     * @param \App\Modules\ModuleComment $comment
     *
     * @param \Engine\Modules\ModuleLang $lang
     *
     * @return \Engine\Result\View\AjaxView
     */
    protected function EventStreamComment(ModuleComment $comment, ModuleLang $lang): AjaxView
    {
        if ($aComments = $comment->GetCommentsOnline('topic', Config::Get('block.stream.row'))) {
            return AjaxView::from([
                'sText' => HtmlView::global("blocks/block.stream_comment.tpl")->with(['aComments' => $aComments])->fetch()
            ]);
        } else {
            return AjaxView::empty()->msgError($lang->Get('block_stream_comments_no'), $lang->Get('attention'), true);
        }
    }

    /**
     * Обработка получения последних топиков
     * Используется в блоке "Прямой эфир"
     *
     * @param \App\Modules\ModuleTopic   $topic
     * @param \Engine\Modules\ModuleLang $lang
     *
     * @return \Engine\Result\View\AjaxView
     */
    protected function EventStreamTopic(ModuleTopic $topic, ModuleLang $lang): AjaxView
    {
        if ($oTopics = $topic->GetTopicsLast(Config::Get('block.stream.row'))) {
            return AjaxView::from([
                'sText' => HtmlView::global("blocks/block.stream_topic.tpl")->with(['oTopics' => $oTopics])->fetch()
            ]);
        } else {
            return AjaxView::empty()->msgError($lang->Get('block_stream_topics_no'), $lang->Get('attention'), true);
        }
    }
}
