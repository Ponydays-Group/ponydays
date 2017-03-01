<?php

class PluginPostingingroups_HookMain extends Hook
{

	/*
	 * Регистрация событий на хуки
	 */
	public function RegisterHook()
	{
		/**
		 * Хук на инициализацию экшенов
		 */
		$this->AddHook('topic_add_after', 'TopicPublish');
//		$this->AddHook('topic_edit_after', 'TopicPublish');
		$this->AddHook('template_form_add_topic_photoset_end', 'AddCheckBox');
		$this->AddHook('template_form_add_topic_topic_end', 'AddCheckBox');
	}

	/**
	 * Публикуем топик в группу в VK
	 * @param $aVars
	 * @return bool
	 */
	public function TopicPublish($aVars)
	{
		/**
		 * Если стоит галка опубликовать
		 */

		/**
		 * Публиковать может только админ
		 */

		$oTopic = $aVars['oTopic'];
		$oTopic = $this->Topic_GetTopicById($oTopic->getId());
		if ($oTopic->getBlog()->getType() != 'open') {
			return false;
		}
		if (!$oTopic->getPublish()) {
			return false;
		}

		if (Config::Get('plugin.postingingroups.vk.access_token') == '') {
			return false;
		}

		$sPhotoId = null;
		$sUrlImage = null;
		if ($oMainPhoto = $oTopic->getPhotosetMainPhoto()) {
			$aSise = Config::Get('module.topic.photoset.size');
			$sSize = $aSise[1]['w'];
			if ($aSise[1]['crop']) {
				$sSize .= 'crop';
			}
			$sUrlImage = $oMainPhoto->getWebPath($sSize);
		} elseif (preg_match('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $oTopic->getText(), $aImages)) {
			$sUrlImage = "";
		}
		if (!empty($sUrlImage)) {
			if (strpos($sUrlImage, Config::Get('path.root.web')) === false) {
				/**
				 * Копируем картинку к себе на сервер
				 */
				$sUrlImageOld = $sUrlImage;
				$sUrlImage = Config::Get('path.root.server') . '/tmp/' . time() . '_' . basename($sUrlImageOld);
				copy($sUrlImageOld, $sUrlImage);
			} else {
				$sUrlImage = $this->GetServerPath($sUrlImage);
			}
			$sPhotoId = $this->PluginPostingingroups_Vk_CreatePhotoAttachment($sUrlImage);
		}
		$oResult = $this->PluginPostingingroups_Vk_WallPostAttachment(strip_tags($oTopic->getTextShort()), $sPhotoId, true, false, $oTopic, $oTopic->getUrl());
		if (isset($oResult->response->post_id)) {
			$oTopic->setExtraValue('vk_post_id', $oResult->response->post_id);
			$this->Topic_UpdateTopicContent($oTopic);
		}
		return true;
	}

	/**
	 * Добавляем чекбокс для публикации в группу
	 */
	public function AddCheckBox($aA)
	{
		return null;
	}

	protected function GetServerPath($sUrl)
	{
		return str_replace(Config::Get('path.root.web'), Config::Get('path.root.server'), $sUrl);
	}
}

?>
