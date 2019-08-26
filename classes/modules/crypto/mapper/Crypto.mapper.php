<?php

class ModuleCrypto_MapperCrypto extends Mapper
{
	public function GetLastKeyFor(string $type): array
	{
		$sql = "SELECT
       				key_id, key_type, expire_time, sec_key
				FROM
					".Config::Get('db.table.keyring')."
				WHERE
					key_type = ?
				ORDER BY key_id DESC LIMIT 1;";
		if($row = $this->oDb->selectRow($sql, $type)) {
			return array(
				$row['key_id'],
				$row['key_type'],
				$row['expire_time'],
				$row['sec_key']
			);
		}
		return null;
	}

	public function GetKeyById(int $kid): array
	{
		$sql = "SELECT
					key_id, key_type, sec_key
				FROM
					".Config::Get('db.table.keyring')."
				WHERE
					key_id = ?;";
		if($row = $this->oDb->selectRow($sql, $kid)) {
			return array(
				$row['key_id'],
				$row['key_type'],
				$row['expire_time'],
				$row['sec_key']
			);
		}
		return null;
	}

	public function AddKey(string $key_type, int $expire_time, string $sec_key): int
	{
		$sql = "INSERT INTO".Config::Get('db.table.keyring')."
				(key_type, expire_time, sec_key)
				VALUES(?, ?, ?)";
		if($id = $this->oDb->query($sql, $key_type, $expire_time, $sec_key)) {
			return $id;
		}
		return false;
	}
}
