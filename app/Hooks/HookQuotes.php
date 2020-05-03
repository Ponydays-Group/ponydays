<?php

use Engine\Hook;

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
		$aQuote = $this->Quotes_GetRandomQuote();

		$this->Viewer_Assign('sQuote', $aQuote['data']);
		$this->Viewer_Assign('iQuoteId',$aQuote['id']);
		return $this->Viewer_Fetch('quote_block.tpl');
	}
}
