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
		echo "mur";

		if ($data === "")
			return 0;

		return $this->oMapper->Add($data);
	}

	/**
	 * Удаление цитаты
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
	 * Обновление содержания цитаты
	 *
	 * @param int $id
	 * @param string $data
	 * @return bool
	 */
	public function UpdateQuote (int $id, string $data): bool {
		if ($data === "" || $id === 0)
			return false;

		if ($this->GetQuoteById($id) === $data)
			return true;

		return $this->oMapper->Update($id, $data);
	}

	/**
	 * Возвращает массив всех цитат
	 *
	 * @return array
	 */
	public function GetQuotes (): array {
		return $this->oMapper->GetArray();
	}

	/**
	 * Возвращает случайную цитату
	 *
	 * @return string
	 */
	public function GetRandomQuote (): string {
		$aQuotes = $this->GetQuotes();

		if ($aQuotes !== []) {
			srand((double)microtime() * 1000000);
			return $aQuotes[rand(0, count($aQuotes))]['data'];
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
}