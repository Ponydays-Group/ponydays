<?php

class PluginPostingingroups_ModuleVk extends ModuleORM
{

	public function Init()
	{
		parent::Init();
		$this->sAccessToken = Config::Get('plugin.postingingroups.vk.access_token');
		$this->iGroupId = Config::Get('plugin.postingingroups.vk.group_id');
	}

	/**
	 * Загружаем фото в контакт
	 * @param $sFilePath
	 * @return mixed
	 * @throws Exception
	 */
	public function CreatePhotoAttachment($sFilePath)
	{
		$oResult = $this->CallMethod('photos.getWallUploadServer', array(
			'gid' => $this->iGroupId
		));
		if (isset($oResult->error->error_msg)) exit($this->Lang_Get('plugin.postingingroups.error.not_allowed_to_load_images'));
		$oCurl = new Curl();
		$oCurl->SetUrl($oResult->response->upload_url);
		$oCurl->SetCurlOpt('CURLOPT_POSTFIELDS', array(
			'photo' => '@' . $sFilePath
		));
		$sUpload = $oCurl->GetResponseBody(true);
		if ($sUpload === false) {
			throw new Exception($oCurl->Error());
		}

		$oCurl->close();
		$oUpload = json_decode($sUpload);
		$oResult = $this->CallMethod('photos.saveWallPhoto', array(
			'server' => $oUpload->server,
			'photo' => $oUpload->photo,
			'hash' => $oUpload->hash,
			'gid' => $this->iGroupId,
		));
		return $oResult->response[0]->id;
	}

	/**
	 * Публикуем запись на стену группы
	 * @param string $sAttachments
	 * @param $sMessage
	 */
	public function WallPostAttachment($sMessage, $sAttachments = '', $bFromGroup = true, $bSigned = true, $oTopic)
	{	
		$tags = '';
		foreach ( $oTopic->getTagsArray() as $value ) {
 			 $tags = $tags . '#' . $value . ' ';
		}
		return $this->CallMethod('wall.post', array(
			'owner_id' => -1 * $this->iGroupId,
			'attachment' => $oTopic->getUrl() . strval($sAttachments),
			'message' => $oTopic->getTitle() . PHP_EOL . PHP_EOL . $sMessage . PHP_EOL . PHP_EOL . $tags,
			'from_group' => $bFromGroup ? 1 : 0,
			'signed' => $bSigned ? 1 : 0
		));
	}

	/**
	 * Вызов метода ApiVK
	 * @param $sMethod
	 * @param $mParams
	 * @return bool|mixed
	 */
	private function CallMethod($sMethod, $mParams)
	{
		if (is_array($mParams)) {
			if (!isset($mParams['access_token'])) {
				if (!$this->sAccessToken) return false;
				$mParams['access_token'] = $this->sAccessToken;
			}
			$oCurl = new Curl();
			$oCurl->SetUrl('https://api.vk.com/method/' . $sMethod);
			$oCurl->SetPostfields($mParams);
			$sResult = $oCurl->GetResponseBody(true);
		} else {
			$sParams = $mParams;
			if (!strpos($mParams, 'access_token')) {
				if (!$this->sAccessToken) return false;
				$sParams = $mParams . '&access_token=' . $this->sAccessToken;
			}
			$oCurl = new Curl();
			$oCurl->SetUrl('https://api.vk.com/method/' . $sMethod . '?'. $sParams);
			$sResult = $oCurl->GetResponseBody(true);
		}
		if ($sResult === false) {
			throw new Exception($oCurl->Error());
		}
		$oCurl->close();
		return json_decode($sResult);
	}
}
