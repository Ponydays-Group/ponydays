<?php

namespace App\Hooks;

use App\Modules\ModuleQuotes;
use Engine\Hook;
use Engine\LS;
use Engine\Modules\ModuleViewer;

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
		$aQuote = LS::Make(ModuleQuotes::class)->GetRandomQuote();

        /** @var \Engine\Modules\ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);
        $viewer->Assign('sQuote', $aQuote['data']);
		$viewer->Assign('iQuoteId',$aQuote['id']);
		return $viewer->Fetch('quote_block.tpl');
	}
}
