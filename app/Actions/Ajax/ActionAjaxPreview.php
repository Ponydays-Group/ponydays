<?php

namespace App\Actions\Ajax;

use App\Entities\EntityTopic;
use App\Modules\ModuleTopic;
use App\Modules\ModuleUser;
use Engine\LS;
use Engine\Modules\ModuleLang;
use Engine\Modules\ModuleText;
use Engine\Modules\ModuleViewer;
use Engine\Result\View\AjaxView;
use Engine\Result\View\HtmlView;
use Engine\Routing\Controller;

class ActionAjaxPreview extends Controller
{
    /**
     * @var \App\Entities\EntityUser
     */
    protected $currentUser = null;

    public function boot()
    {
        /** @var ModuleUser $user */
        $user = LS::Make(ModuleUser::class);
        $this->currentUser = $user->GetUserCurrent();
    }

    /**
     * Предпросмотр топика
     *
     * @param \App\Modules\ModuleTopic     $topic
     * @param \Engine\Modules\ModuleViewer $viewer
     * @param \Engine\Modules\ModuleText   $text
     * @param \Engine\Modules\ModuleLang   $lang
     *
     * @return \Engine\Result\View\AjaxView
     * @throws \Exception
     */
    protected function eventPreviewTopic(ModuleTopic $topic, ModuleViewer $viewer, ModuleText $text, ModuleLang $lang): AjaxView
    {
        /**
         * Пользователь авторизован?
         */
        if (!$this->currentUser) {
            return AjaxView::empty()->msgError($lang->Get('need_authorization'), $lang->Get('error'), true);
        }

        /**
         * Допустимый тип топика?
         */
        if (!$topic->IsAllowTopicType($sType = getRequestStr('topic_type'))) {
            return AjaxView::empty()->msgError($lang->Get('topic_create_type_error'), $lang->Get('error'), true);
        }

        /**
         * Создаем объект топика для валидации данных
         */
        $oTopic = new EntityTopic();
        $oTopic->_setValidateScenario($sType); // зависит от типа топика
        $oTopic->setTitle(strip_tags(getRequestStr('topic_title')));
        $oTopic->setTextSource(getRequestStr('topic_text'));
        $oTopic->setTags(getRequestStr('topic_tags'));
        $oTopic->setDateAdd(date("Y-m-d H:i:s"));
        $oTopic->setUserId($this->currentUser->getId());
        $oTopic->setType($sType);
        /**
         * Валидируем необходимые поля топика
         */
        $oTopic->_Validate([
            'topic_title',
            'topic_text',
            'topic_tags',
            'topic_type'
        ], false);
        if ($oTopic->_hasValidateErrors()) {
            return AjaxView::empty()->msgError($oTopic->_getValidateError(), null, true);
        }

        /**
         * Формируем текст топика
         */
        list($sTextShort, $sTextNew, $sTextCut) = $text->Cut($oTopic->getTextSource());
        $oTopic->setCutText($sTextCut);
        $oTopic->setText($text->Parser($sTextNew));
        $oTopic->setTextShort($text->Parser($sTextShort));
        /**
         * Рендерим шаблон для предпросмотра топика
         */
        $sTemplate = "topic_preview_{$oTopic->getType() }.tpl";
        if (!$viewer->TemplateExists($sTemplate)) {
            $sTemplate = 'topic_preview_topic.tpl';
        }
        $sTextResult = HtmlView::global($sTemplate)->with(['oTopic' => $oTopic])->fetch();

        return AjaxView::from(['sText' => $sTextResult]);
    }

    /**
     * Предпросмотр текста
     *
     * @param \App\Modules\ModuleUser    $user
     * @param \Engine\Modules\ModuleText $text
     *
     * @return \Engine\Result\View\AjaxView
     */
    protected function eventPreviewText(ModuleUser $user, ModuleText $text): AjaxView
    {
        $sText = getRequestStr('text', null, 'post');
        $bSave = getRequest('save', null, 'post');
        /**
         * Экранировать или нет HTML теги
         */
        if ($bSave) {
            $sTextResult = htmlspecialchars($sText);
        } else {
            if (getRequestStr('form_comment_mark') == "on") {
                $sTextResult = $text->Parser($text->Mark($sText));
            } else {
                $sTextResult = $text->Parser($sText);
            }
        }

        $sTextResult = preg_replace_callback(
            '/@(.*?)\((.*?)\)/',
            function ($matches) use ($user) {
                $sLogin = $matches[1];
                $sNick = $matches[2];
                $r = "<a href=\"/profile/".$sLogin."/\" class=\"ls-user\">&#64;".$sNick."</a>";
                if ($oTargetUser = $user->getUserByLogin($sLogin)) {
                    return $r;
                }

                return $matches[0];
            },
            $sTextResult
        );
        $sTextResult = preg_replace_callback(
            '/@([a-zA-Zа-яА-Я0-9-_]+)/',
            function ($matches) use ($user) {
                $sLogin = $matches[1];
                $r = "<a href=\"/profile/".$sLogin."/\" class=\"ls-user\">&#64;".$sLogin."</a>";
                if ($oTargetUser = $user->getUserByLogin($sLogin)) {
                    return $r;
                }

                return $matches[0];
            },
            $sTextResult
        );

        return AjaxView::from(['sText' => $sTextResult]);
    }
}
