<?php


class ModuleCast_MapperCast extends Mapper
{

    protected $oDb;

	public function castExist($sTarget,$iTargerId,$iUserId){
		$sql = "SELECT 
					COUNT(*) as cast_count	 
				FROM 
	 				".Config::Get('plugin.castuser.db.table.user_cast_history')." AS tc
	 			WHERE
	 				target = ?
	 			AND 
	 				target_id = ?d
	 			AND 
	 				user_id = ?d
			";

		$iExistCount = 0;
		
		if ($aRows=$this->oDb->select($sql,$sTarget,$iTargerId,$iUserId)) {
			foreach ($aRows as $iItem) {
				$iExistCount = $iItem['cast_count'];
			}
		}

		return $iExistCount;	
	}
    
	public function saveExist($sTarget,$iTargerId,$iUserId){
		$sql="
			INSERT INTO 
				".Config::Get('plugin.castuser.db.table.user_cast_history')."
			VALUES (NULL,?,?d,?d);
		";
		
		return $this->oDb->query(
			$sql,
			$sTarget,
			$iTargerId,
			$iUserId
		);		
		
	}
	
}