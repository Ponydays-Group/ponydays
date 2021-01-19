<?php
/*-------------------------------------------------------
*
*   LiveStreet Engine Social Networking
*   Copyright © 2008 Mzhelskiy Maxim
*
*--------------------------------------------------------
*
*   Official site: www.livestreet.ru
*   Contact e-mail: rus.engine@gmail.com
*
*   GNU General Public License, version 2:
*   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
---------------------------------------------------------
*/

namespace App\Actions;

use App\Entities\EntityTopic;
use App\Entities\EntityTopicPhoto;
use App\Modules\ModuleACL;
use App\Modules\ModuleBlog;
use App\Modules\ModuleComment;
use App\Modules\ModuleStream;
use App\Modules\ModuleSubscribe;
use App\Modules\ModuleTopic;
use App\Modules\ModuleUser;
use Engine\Action;
use Engine\Config;
use Engine\LS;
use Engine\Modules\ModuleHook;
use Engine\Modules\ModuleLang;
use Engine\Modules\ModuleMessage;
use Engine\Modules\ModuleSecurity;
use Engine\Modules\ModuleText;
use Engine\Modules\ModuleViewer;
use Engine\Router;

/**
 * Обработка УРЛа вида /photoset/ - управление своими топиками(тип: фотосет)
 *
 * @package actions
 * @since   1.0
 */
class ActionPhotoset extends Action
{
    /**
     * Главное меню
     *
     * @var string
     */
    protected $sMenuHeadItemSelect = 'blog';
    /**
     * Меню
     *
     * @var string
     */
    protected $sMenuItemSelect = 'topic';
    /**
     * СубМеню
     *
     * @var string
     */
    protected $sMenuSubItemSelect = 'photoset';
    /**
     * Текущий юзер
     *
     * @var \App\Entities\EntityUser|null
     */
    protected $oUserCurrent = null;

    /**
     * Инициализация
     *
     */
    public function Init()
    {
        /**
         * Проверяем авторизован ли юзер
         */
        $this->oUserCurrent = LS::Make(ModuleUser::class)->GetUserCurrent();
        $this->SetDefaultEvent('add');
        LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('topic_photoset_title'));
        /**
         * Загружаем в шаблон JS текстовки
         */
        LS::Make(ModuleLang::class)->AddLangJs(
            [
                'topic_photoset_photo_delete',
                'topic_photoset_mark_as_preview',
                'topic_photoset_photo_delete_confirm',
                'topic_photoset_is_preview',
                'topic_photoset_upload_choose'
            ]
        );
    }

    /**
     * Регистрируем евенты
     *
     */
    protected function RegisterEvent()
    {
        $this->AddEvent('edit', 'EventEdit'); // Редактирование топика
        $this->AddEvent('deleteimage', 'EventDeletePhoto'); // Удаление изображения
        $this->AddEvent('upload', 'EventUpload'); // Загрузка изображения
        $this->AddEvent('getMore', 'EventGetMore');    // Загрузка изображения на сервер
        $this->AddEvent('setimagedescription', 'EventSetPhotoDescription'); // Установка описания к фото
    }


    /**********************************************************************************
     ************************ РЕАЛИЗАЦИЯ ЭКШЕНА ***************************************
     **********************************************************************************
     */

    /**
     * AJAX подгрузка следующих фото
     *
     */
    protected function EventGetMore()
    {
        /**
         * Устанавливаем формат Ajax ответа
         */
        LS::Make(ModuleViewer::class)->SetResponseAjax('json');
        /**
         * Существует ли топик
         */
        $oTopic = LS::Make(ModuleTopic::class)->getTopicById(getRequestStr('topic_id'));
        if (!$oTopic || !getRequest('last_id')) {
            LS::Make(ModuleMessage::class)->AddError(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        /**
         * Получаем список фото
         */
        $aPhotos = $oTopic->getPhotosetPhotos(getRequestStr('last_id'), Config::Get('module.topic.photoset.per_page'));
        $aResult = [];
        if (count($aPhotos)) {
            /**
             * Формируем данные для ajax ответа
             */
            foreach ($aPhotos as $oPhoto) {
                $aResult[] = [
                    'id'          => $oPhoto->getId(),
                    'path_thumb'  => $oPhoto->getWebPath('50crop'),
                    'path'        => $oPhoto->getWebPath(),
                    'description' => $oPhoto->getDescription()
                ];
            }
            LS::Make(ModuleViewer::class)->AssignAjax('photos', $aResult);
        }
        LS::Make(ModuleViewer::class)->AssignAjax(
            'bHaveNext',
            count($aPhotos) == Config::Get('module.topic.photoset.per_page')
        );
    }

    /**
     * AJAX удаление фото
     *
     */
    protected function EventDeletePhoto()
    {
        /**
         * Устанавливаем формат Ajax ответа
         */
        LS::Make(ModuleViewer::class)->SetResponseAjax('json');
        /**
         * Проверяем авторизован ли юзер
         */
        if (!LS::Make(ModuleUser::class)->IsAuthorization()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('not_access'),
                LS::Make(ModuleLang::class)->Get('error')
            );
            Router::Action('error');

            return;
        }
        /**
         * Поиск фото по id
         */
        $oPhoto = LS::Make(ModuleTopic::class)->getTopicPhotoById(getRequestStr('id'));
        if ($oPhoto) {
            if ($oPhoto->getTopicId()) {
                /**
                 * Проверяем права на топик
                 */
                if ($oTopic = LS::Make(ModuleTopic::class)->GetTopicById($oPhoto->getTopicId()) and LS::Make(
                        ModuleACL::class
                    )->IsAllowEditTopic($oTopic, $this->oUserCurrent)
                ) {
                    if ($oTopic->getPhotosetCount() > 1) {
                        LS::Make(ModuleTopic::class)->deleteTopicPhoto($oPhoto);
                        /**
                         * Если удаляем главную фотку топика, то её необходимо сменить
                         */
                        if ($oPhoto->getId() == $oTopic->getPhotosetMainPhotoId()) {
                            $aPhotos = $oTopic->getPhotosetPhotos(0, 1);
                            $oTopic->setPhotosetMainPhotoId($aPhotos[0]->getId());
                        }
                        $oTopic->setPhotosetCount($oTopic->getPhotosetCount() - 1);
                        LS::Make(ModuleTopic::class)->UpdateTopic($oTopic);
                        LS::Make(ModuleMessage::class)->AddNotice(
                            LS::Make(ModuleLang::class)->Get('topic_photoset_photo_deleted'),
                            LS::Make(ModuleLang::class)->Get('attention')
                        );
                    } else {
                        LS::Make(ModuleMessage::class)->AddError(
                            LS::Make(ModuleLang::class)->Get('topic_photoset_photo_deleted_error_last'),
                            LS::Make(ModuleLang::class)->Get('error')
                        );
                    }

                    return;
                }
            } else {
                LS::Make(ModuleTopic::class)->deleteTopicPhoto($oPhoto);
                LS::Make(ModuleMessage::class)->AddNotice(
                    LS::Make(ModuleLang::class)->Get('topic_photoset_photo_deleted'),
                    LS::Make(ModuleLang::class)->Get('attention')
                );

                return;
            }
        }
        LS::Make(ModuleMessage::class)->AddError(
            LS::Make(ModuleLang::class)->Get('system_error'),
            LS::Make(ModuleLang::class)->Get('error')
        );
    }

    /**
     * AJAX установка описания фото
     *
     */
    protected function EventSetPhotoDescription()
    {
        /**
         * Устанавливаем формат Ajax ответа
         */
        LS::Make(ModuleViewer::class)->SetResponseAjax('json');
        /**
         * Проверяем авторизован ли юзер
         */
        if (!LS::Make(ModuleUser::class)->IsAuthorization()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('not_access'),
                LS::Make(ModuleLang::class)->Get('error')
            );
            Router::Action('error');

            return;
        }
        /**
         * Поиск фото по id
         */
        $oPhoto = LS::Make(ModuleTopic::class)->getTopicPhotoById(getRequestStr('id'));
        if ($oPhoto) {
            if ($oPhoto->getTopicId()) {
                // проверяем права на топик
                if ($oTopic = LS::Make(ModuleTopic::class)->GetTopicById($oPhoto->getTopicId()) and LS::Make(
                        ModuleACL::class
                    )->IsAllowEditTopic($oTopic, $this->oUserCurrent)
                ) {
                    $oPhoto->setDescription(htmlspecialchars(strip_tags(getRequestStr('text'))));
                    LS::Make(ModuleTopic::class)->updateTopicPhoto($oPhoto);
                }
            } else {
                $oPhoto->setDescription(htmlspecialchars(strip_tags(getRequestStr('text'))));
                LS::Make(ModuleTopic::class)->updateTopicPhoto($oPhoto);
            }
        }
    }

    /**
     * AJAX загрузка фоток
     */
    protected function EventUpload()
    {
        /**
         * Устанавливаем формат Ajax ответа
         * В зависимости от типа загрузчика устанавливается тип ответа
         */
        if (getRequest('is_iframe')) {
            LS::Make(ModuleViewer::class)->SetResponseAjax('jsonIframe', false);
        } else {
            LS::Make(ModuleViewer::class)->SetResponseAjax('json');
        }
        /**
         * Проверяем авторизован ли юзер
         */
        if (!LS::Make(ModuleUser::class)->IsAuthorization()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('not_access'),
                LS::Make(ModuleLang::class)->Get('error')
            );
            Router::Action('error');

            return;
        }
        /**
         * Файл был загружен?
         */
        if (!isset($_FILES['Filedata']['tmp_name'])) {
            LS::Make(ModuleMessage::class)->AddError(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        $iTopicId = getRequestStr('topic_id');
        $sTargetId = null;
        $iCountPhotos = 0;
        // Если от сервера не пришёл id топика, то пытаемся определить временный код для нового топика. Если и его нет. то это ошибка
        if (!$iTopicId) {
            $sTargetId = empty($_COOKIE['ls_photoset_target_tmp']) ? getRequestStr('ls_photoset_target_tmp')
                : $_COOKIE['ls_photoset_target_tmp'];
            if (!$sTargetId) {
                LS::Make(ModuleMessage::class)->AddError(
                    LS::Make(ModuleLang::class)->Get('system_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );

                return;
            }
            $iCountPhotos = LS::Make(ModuleTopic::class)->getCountPhotosByTargetTmp($sTargetId);
        } else {
            /**
             * Загрузка фото к уже существующему топику
             */
            $oTopic = LS::Make(ModuleTopic::class)->getTopicById($iTopicId);
            if (!$oTopic or !LS::Make(ModuleACL::class)->IsAllowEditTopic($oTopic, $this->oUserCurrent)) {
                LS::Make(ModuleMessage::class)->AddError(
                    LS::Make(ModuleLang::class)->Get('system_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );

                return;
            }
            $iCountPhotos = LS::Make(ModuleTopic::class)->getCountPhotosByTopicId($iTopicId);
        }
        /**
         * Максимальное количество фото в топике
         */
        if ($iCountPhotos >= Config::Get('module.topic.photoset.count_photos_max')) {
            LS::Make(ModuleMessage::class)->AddError(
                LS::Make(ModuleLang::class)->Get(
                    'topic_photoset_error_too_much_photos',
                    ['MAX' => Config::Get('module.topic.photoset.count_photos_max')]
                ),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        /**
         * Максимальный размер фото
         */
        if (filesize($_FILES['Filedata']['tmp_name']) > Config::Get('module.topic.photoset.photo_max_size') * 1024) {
            LS::Make(ModuleMessage::class)->AddError(
                LS::Make(ModuleLang::class)->Get(
                    'topic_photoset_error_bad_filesize',
                    ['MAX' => Config::Get('module.topic.photoset.photo_max_size')]
                ),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        /**
         * Загружаем файл
         */
        $sFile = LS::Make(ModuleTopic::class)->UploadTopicPhoto($_FILES['Filedata']);
        if ($sFile) {
            /**
             * Создаем фото
             */
            $oPhoto = new EntityTopicPhoto();
            $oPhoto->setPath($sFile);
            if ($iTopicId) {
                $oPhoto->setTopicId($iTopicId);
            } else {
                $oPhoto->setTargetTmp($sTargetId);
            }
            if ($oPhoto = LS::Make(ModuleTopic::class)->addTopicPhoto($oPhoto)) {
                /**
                 * Если топик уже существует (редактирование), то обновляем число фоток в нём
                 */
                if (isset($oTopic)) {
                    $oTopic->setPhotosetCount($oTopic->getPhotosetCount() + 1);
                    LS::Make(ModuleTopic::class)->UpdateTopic($oTopic);
                }

                LS::Make(ModuleViewer::class)->AssignAjax('file', $oPhoto->getWebPath('100crop'));
                LS::Make(ModuleViewer::class)->AssignAjax('id', $oPhoto->getId());
                LS::Make(ModuleMessage::class)->AddNotice(
                    LS::Make(ModuleLang::class)->Get('topic_photoset_photo_added'),
                    LS::Make(ModuleLang::class)->Get('attention')
                );
            } else {
                LS::Make(ModuleMessage::class)->AddError(
                    LS::Make(ModuleLang::class)->Get('system_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );
            }
        } else {
            LS::Make(ModuleMessage::class)->AddError(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );
        }
    }

    /**
     * Редактирование топика
     *
     */
    protected function EventEdit()
    {
        /**
         * Проверяем авторизован ли юзер
         */
        if (!LS::Make(ModuleUser::class)->IsAuthorization()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('not_access'),
                LS::Make(ModuleLang::class)->Get('error')
            );
            Router::Action('error');

            return;
        }
        /**
         * Получаем номер топика из УРЛ и проверяем существует ли он
         */
        $sTopicId = $this->GetParam(0);
        if (!($oTopic = LS::Make(ModuleTopic::class)->GetTopicById($sTopicId))) {
            parent::EventNotFound();

            return;
        }
        /**
         * Проверяем тип топика
         */
        if ($oTopic->getType() != 'photoset') {
            parent::EventNotFound();

            return;
        }
        /**
         * Если права на редактирование
         */
        if (!LS::Make(ModuleACL::class)->IsAllowEditTopic($oTopic, $this->oUserCurrent)) {
            parent::EventNotFound();

            return;
        }
        /**
         * Вызов хуков
         */
        LS::Make(ModuleHook::class)->Run('topic_edit_show', ['oTopic' => $oTopic]);
        /**
         * Загружаем переменные в шаблон
         */
        LS::Make(ModuleViewer::class)->Assign(
            'aBlogsAllow',
            LS::Make(ModuleBlog::class)->GetBlogsAllowByUser($this->oUserCurrent)
        );
        LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('topic_photoset_title_edit'));
        /**
         * Устанавливаем шаблон вывода
         */
        $this->SetTemplateAction('add');

        if (!is_numeric(getRequest('topic_id'))) {
            $_REQUEST['topic_id'] = '';
        }
        /**
         * Проверяем отправлена ли форма с данными(хотяб одна кнопка)
         */
        if (isset($_REQUEST['submit_topic_publish']) or isset($_REQUEST['submit_topic_save'])) {
            /**
             * Обрабатываем отправку формы
             */
            $this->SubmitEdit($oTopic);

            return;
        } else {
            /**
             * Заполняем поля формы для редактирования
             * Только перед отправкой формы!
             */
            $_REQUEST['topic_title'] = $oTopic->getTitle();
            $_REQUEST['topic_text'] = $oTopic->getTextSource();
            $_REQUEST['topic_tags'] = $oTopic->getTags();
            $_REQUEST['blog_id'] = $oTopic->getBlogId();
            $_REQUEST['topic_id'] = $oTopic->getId();
            $_REQUEST['topic_publish_index'] = $oTopic->getPublishIndex();
            $_REQUEST['topic_forbid_comment'] = $oTopic->getForbidComment();
            $_REQUEST['topic_main_photo'] = $oTopic->getPhotosetMainPhotoId();
        }
        LS::Make(ModuleViewer::class)->Assign(
            'aPhotos',
            LS::Make(ModuleTopic::class)->getPhotosByTopicId($oTopic->getId())
        );
    }

    /**
     * Добавление топика
     *
     */
    protected function EventAdd()
    {
        /**
         * Проверяем авторизован ли юзер
         */
        if (!LS::Make(ModuleUser::class)->IsAuthorization()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('not_access'),
                LS::Make(ModuleLang::class)->Get('error')
            );
            Router::Action('error');

            return;
        }
        /**
         * Вызов хуков
         */
        LS::Make(ModuleHook::class)->Run('topic_add_show');
        /**
         * Загружаем переменные в шаблон
         */
        LS::Make(ModuleViewer::class)->Assign(
            'aBlogsAllow',
            LS::Make(ModuleBlog::class)->GetBlogsAllowByUser($this->oUserCurrent)
        );
        LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('topic_photoset_title_create'));

        if (!is_numeric(getRequest('topic_id'))) {
            $_REQUEST['topic_id'] = '';
        }
        /**
         * Если нет временного ключа для нового топика, то генерируеи. если есть, то загружаем фото по этому ключу
         */
        if (empty($_COOKIE['ls_photoset_target_tmp'])) {
            setcookie(
                'ls_photoset_target_tmp',
                func_generator(),
                time() + 24 * 3600,
                Config::Get('sys.cookie.path'),
                Config::Get('sys.cookie.host')
            );
        } else {
            setcookie(
                'ls_photoset_target_tmp',
                $_COOKIE['ls_photoset_target_tmp'],
                time() + 24 * 3600,
                Config::Get('sys.cookie.path'),
                Config::Get('sys.cookie.host')
            );
            LS::Make(ModuleViewer::class)->Assign(
                'aPhotos',
                LS::Make(ModuleTopic::class)->getPhotosByTargetTmp($_COOKIE['ls_photoset_target_tmp'])
            );
        }
        /**
         * Обрабатываем отправку формы
         */
        $this->SubmitAdd();

        return;
    }

    /**
     * Обработка добавлени топика
     */
    protected function SubmitAdd()
    {
        /**
         * Проверяем отправлена ли форма с данными(хотяб одна кнопка)
         */
        if (!isPost('submit_topic_publish') and !isPost('submit_topic_save')) {
            return;
        }
        $oTopic = new EntityTopic();
        $oTopic->_setValidateScenario('photoset');
        /**
         * Заполняем поля для валидации
         */
        $oTopic->setBlogId(getRequestStr('blog_id'));
        $oTopic->setTitle(strip_tags(getRequestStr('topic_title')));
        $oTopic->setTextSource(getRequestStr('topic_text'));
        $oTopic->setTags(getRequestStr('topic_tags'));
        $oTopic->setUserId($this->oUserCurrent->getId());
        $oTopic->setType('photoset');
        $oTopic->setDateAdd(date("Y-m-d H:i:s"));
        $oTopic->setUserIp(func_getIp());
        /**
         * Проверка корректности полей формы
         */
        if (!$this->checkTopicFields($oTopic)) {
            return;
        }
        /**
         * Определяем в какой блог делаем запись
         */
        $iBlogId = $oTopic->getBlogId();
        if ($iBlogId == 0) {
            $oBlog = LS::Make(ModuleBlog::class)->GetPersonalBlogByUserId($this->oUserCurrent->getId());
        } else {
            $oBlog = LS::Make(ModuleBlog::class)->GetBlogById($iBlogId);
        }
        /**
         * Если блог не определен выдаем предупреждение
         */
        if (!$oBlog) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('topic_create_blog_error_unknown'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        /**
         * Проверяем права на постинг в блог
         */
        if (!LS::Make(ModuleACL::class)->IsAllowBlog($oBlog, $this->oUserCurrent)) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('topic_create_blog_error_noallow'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        /**
         * Проверяем разрешено ли постить топик по времени
         */
        if (isPost('submit_topic_publish') and !LS::Make(ModuleACL::class)->CanPostTopicTime($this->oUserCurrent)) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('topic_time_limit'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        /**
         * Теперь можно смело добавлять топик к блогу
         */
        $oTopic->setBlogId($oBlog->getId());
        /**
         * Получаемый и устанавливаем разрезанный текст по тегу <cut>
         */
        list($sTextShort, $sTextNew, $sTextCut) = LS::Make(ModuleText::class)->Cut($oTopic->getTextSource());
        $oTopic->setCutText($sTextCut);
        $oTopic->setText(LS::Make(ModuleText::class)->Parser($sTextNew));
        $oTopic->setTextShort(LS::Make(ModuleText::class)->Parser($sTextShort));

        $sTargetTmp = $_COOKIE['ls_photoset_target_tmp'];
        $aPhotos = LS::Make(ModuleTopic::class)->getPhotosByTargetTmp($sTargetTmp);
        if (!($oPhotoMain = LS::Make(ModuleTopic::class)->getTopicPhotoById(getRequestStr('topic_main_photo'))
            and $oPhotoMain->getTargetTmp() == $sTargetTmp)
        ) {
            $oPhotoMain = $aPhotos[0];
        }
        $oTopic->setPhotosetMainPhotoId($oPhotoMain->getId());
        $oTopic->setPhotosetCount(count($aPhotos));
        /**
         * Публикуем или сохраняем
         */
        if (isset($_REQUEST['submit_topic_publish'])) {
            $oTopic->setPublish(1);
            $oTopic->setPublishDraft(1);
        } else {
            $oTopic->setPublish(0);
            $oTopic->setPublishDraft(0);
        }
        /**
         * Принудительный вывод на главную
         */
        $oTopic->setPublishIndex(0);
        if (LS::Make(ModuleACL::class)->IsAllowPublishIndex($this->oUserCurrent)) {
            if (getRequest('topic_publish_index')) {
                $oTopic->setPublishIndex(1);
            }
        }
        /**
         * Запрет на комментарии к топику
         */
        $oTopic->setForbidComment(0);
        if (getRequest('topic_forbid_comment')) {
            $oTopic->setForbidComment(1);
        }
        /**
         * Запускаем выполнение хуков
         */
        LS::Make(ModuleHook::class)->Run('topic_add_before', ['oTopic' => $oTopic, 'oBlog' => $oBlog]);
        /**
         * Добавляем топик
         */
        if (LS::Make(ModuleTopic::class)->AddTopic($oTopic)) {
            LS::Make(ModuleHook::class)->Run('topic_add_after', ['oTopic' => $oTopic, 'oBlog' => $oBlog]);
            /**
             * Получаем топик, чтоб подцепить связанные данные
             */
            $oTopic = LS::Make(ModuleTopic::class)->GetTopicById($oTopic->getId());
            /**
             * Обновляем количество топиков в блоге
             */
            LS::Make(ModuleBlog::class)->RecalculateCountTopicByBlogId($oTopic->getBlogId());
            /**
             * Добавляем автора топика в подписчики на новые комментарии к этому топику
             */
            LS::Make(ModuleSubscribe::class)->AddSubscribeSimple(
                'topic_new_comment',
                $oTopic->getId(),
                $this->oUserCurrent->getMail()
            );
            /**
             * Делаем рассылку спама всем, кто состоит в этом блоге
             */
            if ($oTopic->getPublish() == 1 and $oBlog->getType() != 'personal') {
                LS::Make(ModuleTopic::class)->SendNotifyTopicNew($oBlog, $oTopic, $this->oUserCurrent);
            }
            /**
             * Привязываем фото к id топика
             * здесь нужно это делать одним запросом, а не перебором сущностей
             */
            if (count($aPhotos)) {
                foreach ($aPhotos as $oPhoto) {
                    $oPhoto->setTargetTmp(null);
                    $oPhoto->setTopicId($oTopic->getId());
                    LS::Make(ModuleTopic::class)->updateTopicPhoto($oPhoto);
                }
            }
            /**
             * Удаляем временную куку
             */
            setcookie('ls_photoset_target_tmp', null);
            /**
             * Добавляем событие в ленту
             */
            LS::Make(ModuleStream::class)->write(
                $oTopic->getUserId(),
                'add_topic',
                $oTopic->getId(),
                $oTopic->getPublish() && $oBlog->getType() != 'close'
            );
            Router::Location($oTopic->getUrl());
        } else {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('system_error'));
            Router::Action('error');

            return;
        }
    }

    /**
     * Обработка редактирования топика
     *
     * @param \App\Entities\EntityTopic $oTopic
     */
    protected function SubmitEdit($oTopic)
    {
        $oTopic->_setValidateScenario('photoset');
        /**
         * Сохраняем старое значение идентификатора блога
         */
        $sBlogIdOld = $oTopic->getBlogId();
        /**
         * Заполняем поля для валидации
         */
        $oTopic->setBlogId(getRequestStr('blog_id'));
        $oTopic->setTitle(strip_tags(getRequestStr('topic_title')));
        $oTopic->setTextSource(getRequestStr('topic_text'));
        $oTopic->setTags(getRequestStr('topic_tags'));
        $oTopic->setUserIp(func_getIp());
        /**
         * Проверка корректности полей формы
         */
        if (!$this->checkTopicFields($oTopic)) {
            return;
        }
        /**
         * Определяем в какой блог делаем запись
         */
        $iBlogId = $oTopic->getBlogId();
        if ($iBlogId == 0) {
            $oBlog = LS::Make(ModuleBlog::class)->GetPersonalBlogByUserId($oTopic->getUserId());
        } else {
            $oBlog = LS::Make(ModuleBlog::class)->GetBlogById($iBlogId);
        }
        /**
         * Если блог не определен выдаем предупреждение
         */
        if (!$oBlog) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('topic_create_blog_error_unknown'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        /**
         * Проверяем права на постинг в блог
         */
        if (!LS::Make(ModuleACL::class)->IsAllowBlog($oBlog, $this->oUserCurrent)) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('topic_create_blog_error_noallow'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        /**
         * Проверяем разрешено ли постить топик по времени
         */
        if (isPost('submit_topic_publish') and !$oTopic->getPublishDraft() and !LS::Make(ModuleACL::class)
                ->CanPostTopicTime($this->oUserCurrent)
        ) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('topic_time_limit'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        /**
         * Теперь можно смело редактировать топик
         */
        $oTopic->setBlogId($oBlog->getId());
        /**
         * Получаемый и устанавливаем разрезанный текст по тегу <cut>
         */
        list($sTextShort, $sTextNew, $sTextCut) = LS::Make(ModuleText::class)->Cut($oTopic->getTextSource());
        $oTopic->setCutText($sTextCut);
        $oTopic->setText(LS::Make(ModuleText::class)->Parser($sTextNew));
        $oTopic->setTextShort(LS::Make(ModuleText::class)->Parser($sTextShort));

        $aPhotos = $oTopic->getPhotosetPhotos();
        if (!($oPhotoMain = LS::Make(ModuleTopic::class)->getTopicPhotoById(getRequestStr('topic_main_photo'))
            and $oPhotoMain->getTopicId() == $oTopic->getId())
        ) {
            $oPhotoMain = $aPhotos[0];
        }
        $oTopic->setPhotosetMainPhotoId($oPhotoMain->getId());
        $oTopic->setPhotosetCount(count($aPhotos));
        /**
         * Публикуем или сохраняем в черновиках
         */
        $bSendNotify = false;
        if (isset($_REQUEST['submit_topic_publish'])) {
            $oTopic->setPublish(1);
            if ($oTopic->getPublishDraft() == 0) {
                $oTopic->setPublishDraft(1);
                $oTopic->setDateAdd(date("Y-m-d H:i:s"));
                $bSendNotify = true;
            }
        } else {
            $oTopic->setPublish(0);
        }
        /**
         * Принудительный вывод на главную
         */
        if (LS::Make(ModuleACL::class)->IsAllowPublishIndex($this->oUserCurrent)) {
            if (getRequest('topic_publish_index')) {
                $oTopic->setPublishIndex(1);
            } else {
                $oTopic->setPublishIndex(0);
            }
        }
        /**
         * Запрет на комментарии к топику
         */
        $oTopic->setForbidComment(0);
        if (getRequest('topic_forbid_comment')) {
            $oTopic->setForbidComment(1);
        }
        LS::Make(ModuleHook::class)->Run('topic_edit_before', ['oTopic' => $oTopic, 'oBlog' => $oBlog]);
        /**
         * Сохраняем топик
         */
        if (LS::Make(ModuleTopic::class)->UpdateTopic($oTopic)) {
            LS::Make(ModuleHook::class)->Run(
                'topic_edit_after',
                ['oTopic' => $oTopic, 'oBlog' => $oBlog, 'bSendNotify' => &$bSendNotify]
            );
            /**
             * Обновляем данные в комментариях, если топик был перенесен в новый блог
             */
            if ($sBlogIdOld != $oTopic->getBlogId()) {
                LS::Make(ModuleComment::class)->UpdateTargetParentByTargetId(
                    $oTopic->getBlogId(),
                    'topic',
                    $oTopic->getId()
                );
                LS::Make(ModuleComment::class)->UpdateTargetParentByTargetIdOnline(
                    $oTopic->getBlogId(),
                    'topic',
                    $oTopic->getId()
                );
            }
            /**
             * Обновляем количество топиков в блоге
             */
            if ($sBlogIdOld != $oTopic->getBlogId()) {
                LS::Make(ModuleBlog::class)->RecalculateCountTopicByBlogId($sBlogIdOld);
            }
            LS::Make(ModuleBlog::class)->RecalculateCountTopicByBlogId($oTopic->getBlogId());
            /**
             * Добавляем событие в ленту
             */
            LS::Make(ModuleStream::class)->write(
                $oTopic->getUserId(),
                'add_topic',
                $oTopic->getId(),
                $oTopic->getPublish() && $oBlog->getType() != 'close'
            );
            /**
             * Рассылаем о новом топике подписчикам блога
             */
            if ($bSendNotify) {
                LS::Make(ModuleTopic::class)->SendNotifyTopicNew($oBlog, $oTopic, $this->oUserCurrent);
            }
            if (!$oTopic->getPublish() and !$this->oUserCurrent->isAdministrator() and $this->oUserCurrent->getId()
                != $oTopic->getUserId()
            ) {
                Router::Location($oBlog->getUrlFull());
            }
            Router::Location($oTopic->getUrl());
        } else {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('system_error'));
            Router::Action('error');

            return;
        }
    }

    /**
     * Проверка полей формы
     *
     * @return bool
     */
    protected function checkTopicFields($oTopic)
    {
        LS::Make(ModuleSecurity::class)->ValidateSendForm();

        $bOk = true;
        if (!$oTopic->_Validate()) {
            LS::Make(ModuleMessage::class)->AddError(
                $oTopic->_getValidateError(),
                LS::Make(ModuleLang::class)->Get('error')
            );
            $bOk = false;
        }

        $sTargetId = null;
        $iCountPhotos = 0;
        if (!$oTopic->getTopicId()) {
            if (isset($_COOKIE['ls_photoset_target_tmp'])) {
                $iCountPhotos =
                    LS::Make(ModuleTopic::class)->getCountPhotosByTargetTmp($_COOKIE['ls_photoset_target_tmp']);
            } else {
                LS::Make(ModuleMessage::class)->AddError(
                    LS::Make(ModuleLang::class)->Get('system_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );

                return false;
            }
        } else {
            $iCountPhotos = LS::Make(ModuleTopic::class)->getCountPhotosByTopicId($oTopic->getId());
        }
        if ($iCountPhotos < Config::Get('module.topic.photoset.count_photos_min')
            || $iCountPhotos > Config::Get(
                'module.topic.photoset.count_photos_max'
            )
        ) {
            LS::Make(ModuleMessage::class)->AddError(
                LS::Make(ModuleLang::class)->Get(
                    'topic_photoset_error_count_photos',
                    [
                        'MIN' => Config::Get('module.topic.photoset.count_photos_min'),
                        'MAX' => Config::Get('module.topic.photoset.count_photos_max')
                    ]
                ),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return false;
        }
        /**
         * Выполнение хуков
         */
        LS::Make(ModuleHook::class)->Run('check_photoset_fields', ['bOk' => &$bOk]);

        return $bOk;
    }

    /**
     * При завершении экшена загружаем необходимые переменные
     *
     */
    public function EventShutdown()
    {
        LS::Make(ModuleViewer::class)->Assign('sMenuHeadItemSelect', $this->sMenuHeadItemSelect);
        LS::Make(ModuleViewer::class)->Assign('sMenuItemSelect', $this->sMenuItemSelect);
        LS::Make(ModuleViewer::class)->Assign('sMenuSubItemSelect', $this->sMenuSubItemSelect);
    }
}
