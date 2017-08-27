<?php
/**
 * Модуль цитатника
 *
 * User: silvman
 * Date: 19.08.17
 * Time: 3:11
 */

class ModuleQuotes extends Module {
	/**
	 * Объект маппера
	 *
	 * @var
	 */
	protected $oMapper;

	/**
	 * Инициализация
	 */
	public function Init () {
		$this->oMapper = Engine::GetMapper(__CLASS__);
	}

	/* ------ Тело модуля ------- */

	/**
	 * Добавление цитаты
	 *
	 * @param string $data
	 * @return int
	 */
	public function AddQuote (string $data): int {
		$data = $this->Text_Parser($data);

		if (!func_check($data,'text',2,Config::Get('module.comment.max_length')))
			return 0;

		return $this->oMapper->Add($data);
	}

	/**
	 * Безопасное удаление цитаты
	 *
	 * @param int $id
	 * @return bool
	 */
	public function DeleteQuote (int $id): bool {
		if ($id === 0)
			return false;

		return $this->oMapper->Delete($id);
	}

	/**
	 * Восстановление цитаты
	 *
	 * @param int $id
	 * @return bool
	 */
	public function RestoreQuote (int $id): bool {
		if($id === 0)
			return false;

		return $this->oMapper->Restore($id);
	}

	/**
	 * Обновление содержания цитаты
	 *
	 * @param int $id
	 * @param string $data
	 * @return bool
	 */
	public function UpdateQuote (int $id, string $data): bool {
		$data = $this->Text_Parser($data);

		if ($id === 0 || !func_check($data,'text',2,Config::Get('module.comment.max_length')))
			return false;

		if ($this->GetQuoteById($id) === $data)
			return true;

		return $this->oMapper->Update($id, $data);
	}

	/**
	 * Возвращает массив всех неудалённых цитат
	 *
	 * @return array
	 */
	public function GetQuotes (): array {
		return $this->oMapper->GetArray();
	}

	/**
	 * Возвращает цитаты для постраничного вывода
	 *
	 * @param int $iCurrPage
	 * @param int $iPerPage
	 * @return array
	 */
	public function GetQuotesForPage (int $iCurrPage, int $iPerPage): array {
		return $this->oMapper->GetArrayForPage($iCurrPage, $iPerPage);
	}

	/**
	 * Возвращает случайную цитату
	 *
	 * @return string
	 */
	public function GetRandomQuote (): string {
		if ($id = $this->GetRandomId()) {
			srand((double)microtime() * 1000000);
			return($this->GetQuoteById($id));
		}

		return "";
	}

	/**
	 * Существует ли такая цитата
	 *
	 * @param int $id
	 * @return bool
	 */
	public function IsQuoteExistsById (int $id): bool {
		if ($this->GetQuoteById($id) !== "") {
			return true;
		}

		return false;
	}

	/**
	 * Возвращает цитату, если существует
	 *
	 * @param int $id
	 * @return string
	 */
	public function GetQuoteById (int $id): string {
		return $this->oMapper->GetById($id);
	}

	/**
	 * Возвращает рандомноайдишник
	 *
	 * @return int
	 */
	public function GetRandomId (): int {
		$aIds = $this->oMapper->GetIds();

		if($aIds !== []) {
			srand((double)microtime() * 1000000);
			return $aIds[rand(0, $this->oMapper->GetCount())];
		}

		return 0;
	}

	/**
	 * Возвращает страницу, на которой располагается цитата
	 *
	 * @param int $id
	 * @return int
	 */
	public function GetPageById (int $id): int {
		if($this->IsQuoteExistsById($id)) {
			$aIds = $this->oMapper->GetIds();

			$iNum = 1;
			foreach ($aIds as $iId) {
				if ((int)$iId === $id)
					break;

				$iNum++;
			}

			return ceil($iNum / Config::Get('module.quotes.per_page'));
		}

		return 0;
	}

	/**
	 * Возвращает массив удалённых цитат
	 *
	 * @return array
	 */
	public function GetDeletedQuotes (): array {
		return $this->oMapper->GetArray( /* $bDeleted */ true);
	}

	/**
	 * Возвращает количество цитат
	 *
	 * @return int
	 */
	public function GetCount (): int {
		return $this->oMapper->GetCount();
	}


}