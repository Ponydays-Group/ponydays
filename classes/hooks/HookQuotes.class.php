<?php
/**
 * User: silvman
 * Date: 20.08.17
 * Time: 0:59
 */

class HookQuotes extends Hook {
	public function RegisterHook() {
		$this->AddHook('template_quotes_generator','GetQuote');
	}

	public function GetQuote() {
		return $this->Quotes_GetRandomQuote();
	}
}