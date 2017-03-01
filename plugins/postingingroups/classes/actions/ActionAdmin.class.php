<?php


class PluginPostingingroups_ActionAdmin extends ActionPlugin
{

	public function Init()
	{
	}


	protected function RegisterEvent()
	{
		$this->AddEvent('admin', 'EventGetAccessToken');
	}


	/**********************************************************************************
	 ************************ РЕАЛИЗАЦИЯ ЭКШЕНА ***************************************
	 **********************************************************************************
	 */

	/**
	 * Получение токена в админке
	 */
	protected function EventGetAccessToken()
	{
		if (!LS::Adm()) return parent::EventNotFound();
		$this->SetTemplateAction('access_token');
	}

}