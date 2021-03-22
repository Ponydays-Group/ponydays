<?php

namespace App\Actions\Ajax;

use App\Modules\ModuleBlog;
use App\Modules\ModuleUser;
use Engine\Config;
use Engine\LS;
use Engine\Modules\ModuleLang;
use Engine\Result\View\AjaxView;
use Engine\Result\View\HtmlView;
use Engine\Routing\Controller;

class ActionAjaxBlogs extends Controller
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
     * Обработка получения TOP блогов
     * Используется в блоке "TOP блогов"
     *
     * @param \App\Modules\ModuleBlog    $blog
     * @param \Engine\Modules\ModuleLang $lang
     *
     * @return \Engine\Result\View\AjaxView
     */
    protected function EventBlogsTop(ModuleBlog $blog, ModuleLang $lang): AjaxView
    {
        /**
         * Получаем список блогов и формируем ответ
         */
        if ($aResult = $blog->GetBlogsRating(1, Config::Get('block.blogs.row'))) {
            $aBlogs = $aResult['collection'];

            return AjaxView::from([
                'sText' => HtmlView::global("blocks/block.blogs_top.tpl")->with(['aBlogs' => $aBlogs])->fetch()
            ]);
        } else {
            return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
        }
    }

    /**
     * Обработка получения своих блогов
     * Используется в блоке "TOP блогов"
     *
     * @param \App\Modules\ModuleBlog    $blog
     * @param \Engine\Modules\ModuleLang $lang
     *
     * @return \Engine\Result\View\AjaxView
     */
    protected function EventBlogsSelf(ModuleBlog $blog, ModuleLang $lang): AjaxView
    {
        /**
         * Пользователь авторизован?
         */
        if (!$this->currentUser) {
            return AjaxView::empty()->msgError($lang->Get('need_authorization'), $lang->Get('error'), true);
        }

        /**
         * Получаем список блогов и формируем ответ
         */
        if ($aBlogs = $blog->GetBlogsRatingSelf($this->currentUser->getId(), Config::Get('block.blogs.row'))) {
            return AjaxView::from([
                'sText' => HtmlView::global("blocks/block.blogs_top.tpl")->with(['aBlogs' => $aBlogs])->fetch()
            ]);
        } else {
            return AjaxView::empty()->msgError($lang->Get('block_blogs_self_error'), $lang->Get('attention'), true);
        }
    }

    /**
     * Обработка получения подключенных блогов
     * Используется в блоке "TOP блогов"
     *
     * @param \App\Modules\ModuleBlog    $blog
     * @param \Engine\Modules\ModuleLang $lang
     *
     * @return \Engine\Result\View\AjaxView
     */
    protected function EventBlogsJoin(ModuleBlog $blog, ModuleLang $lang): AjaxView
    {
        /**
         * Пользователь авторизован?
         */
        if (!$this->currentUser) {
            return AjaxView::empty()->msgError($lang->Get('need_authorization'), $lang->Get('error'), true);
        }

        /**
         * Получаем список блогов и формируем ответ
         */
        if ($aBlogs = $blog->GetBlogsRatingJoin($this->currentUser->getId(), Config::Get('block.blogs.row'))) {
            return AjaxView::from([
                'sText' => HtmlView::global("blocks/block.blogs_top.tpl")->with(['aBlogs' => $aBlogs])->fetch()
            ]);
        } else {
            return AjaxView::empty()->msgError($lang->Get('block_blogs_join_error'), $lang->Get('attention'), true);
        }
    }
}
