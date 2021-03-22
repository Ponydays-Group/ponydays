<?php

namespace App\Actions\Ajax;

use App\Modules\ModuleTopic;
use App\Modules\ModuleUser;
use Engine\LS;
use Engine\Modules\ModuleImage;
use Engine\Modules\ModuleLang;
use Engine\Result\View\AjaxView;
use Engine\Routing\Controller;

class ActionAjaxUpload extends Controller
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
     * Загрузка изображения
     *
     * @param \App\Modules\ModuleTopic    $topic
     * @param \Engine\Modules\ModuleImage $image
     * @param \Engine\Modules\ModuleLang  $lang
     *
     * @return \Engine\Result\View\AjaxView
     */
    protected function eventUploadImage(ModuleTopic $topic, ModuleImage $image, ModuleLang $lang): AjaxView
    {
        /**
         * Пользователь авторизован?
         */
        if (!$this->currentUser) {
            return AjaxView::empty()->msgError($lang->Get('need_authorization'), $lang->Get('error'), true);
        }

        $sFile = null;
        $sText = '';
        if (isPost('img_url') && $_REQUEST['img_url'] != '' && $_REQUEST['img_url'] != 'http://') {
            /**
             * Загрузка файла по URl
             */
            $sFile = $topic->UploadTopicImageUrl($_REQUEST['img_url'], $this->currentUser);
            switch (true) {
                case is_string($sFile):
                    break;

                case ($sFile == ModuleImage::UPLOAD_IMAGE_ERROR_READ):
                    return AjaxView::empty()->msgError($lang->Get('uploadimg_url_error_read'), $lang->Get('error'), true);
                case ($sFile == ModuleImage::UPLOAD_IMAGE_ERROR_SIZE):
                    return AjaxView::empty()->msgError($lang->Get('uploadimg_url_error_size'), $lang->Get('error'), true);
                case ($sFile == ModuleImage::UPLOAD_IMAGE_ERROR_TYPE):
                    return AjaxView::empty()->msgError($lang->Get('uploadimg_url_error_type'), $lang->Get('error'), true);
                default:
                case ($sFile == ModuleImage::UPLOAD_IMAGE_ERROR):
                    return AjaxView::empty()->msgError($lang->Get('uploadimg_url_error'), $lang->Get('error'), true);
            }

            if ($sFile) {
                $sText = $image->BuildHTML($sFile, $_REQUEST);
            }
        } elseif (isPost('img_base64')) {
            /**
             * Загрузка файла из Base64
             */
            $sFile = $topic->UploadTopicImageBase64($_REQUEST['img_base64'], $this->currentUser);
            switch (true) {
                case is_string($sFile):
                    break;

                case ($sFile == ModuleImage::UPLOAD_IMAGE_ERROR_READ):
                    return AjaxView::empty()->msgError($lang->Get('uploadimg_url_error_read'), $lang->Get('error'), true);
                case ($sFile == ModuleImage::UPLOAD_IMAGE_ERROR_SIZE):
                    return AjaxView::empty()->msgError($lang->Get('uploadimg_url_error_size'), $lang->Get('error'), true);
                case ($sFile == ModuleImage::UPLOAD_IMAGE_ERROR_TYPE):
                    return AjaxView::empty()->msgError($lang->Get('uploadimg_url_error_type'), $lang->Get('error'), true);
                default:
                case ($sFile == ModuleImage::UPLOAD_IMAGE_ERROR):
                    return AjaxView::empty()->msgError($lang->Get('uploadimg_url_error'), $lang->Get('error'), true);
            }

            if ($sFile) {
                $sText = $image->BuildHTML($sFile, $_REQUEST);
            }
        } else {
            function reArrayFiles(&$file_post)
            {
                $file_ary = [];
                $file_count = count($file_post['name']);
                $file_keys = array_keys($file_post);

                for ($i = 0; $i < $file_count; $i++) {
                    foreach ($file_keys as $key) {
                        $file_ary[$i][$key] = $file_post[$key][$i];
                    }
                }

                return $file_ary;
            }

            $sText = "";
            $aFiles = reArrayFiles($_FILES['img_file']);

            foreach ($aFiles as $k => $v) {
                /**
                 * Был выбран файл с компьютера и он успешно зугрузился?
                 */

                if (is_uploaded_file($v['tmp_name'])) {
                    if (!$sFile = $topic->UploadTopicImageFile($v, $this->currentUser)) {
                        return AjaxView::empty()->msgError($lang->Get('uploadimg_file_error'), $lang->Get('error'), true);
                    }

                    /**
                     * Если файл успешно загружен, формируем HTML вставки и возвращаем в ajax ответе
                     */
                    if ($sFile) {
                        if ($_REQUEST['just_url']) {
                            $sText .= $sFile;
                        } else {
                            $sText .= $image->BuildHTML($sFile, $_REQUEST);
                        }
                    }
                }
            } //foreach
        }

        return AjaxView::from(['sText' => $sText]);
    }
}
