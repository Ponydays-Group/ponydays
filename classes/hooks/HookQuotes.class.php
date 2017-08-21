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
		$iId = $this->Quotes_GetRandomId();

		$this->Viewer_Assign('sQuote', $this->Quotes_GetQuoteById($iId));
		$this->Viewer_Assign('iQuoteId',$iId);
		return $this->Viewer_Fetch('quote_block.tpl');
	}
}