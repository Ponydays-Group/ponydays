<?php

class ModuleFeedbacks_MapperFeedbacks extends Mapper{

	//**************************************************************************************************
	protected function TableName($sShortName){
		return Config::Get('db.table.'.$sShortName);
	}
	
	//**************************************************************************************************

	protected function BuildWhereStatement($aFilter){
		$sWhere = 'WHERE 1=1 ';
		
		return $sWhere;
	}
	
	//**************************************************************************************************
	protected function BuildOrderStatement($aFilter){
		$sOrder = "";
		if(isset($aFilter['Order']))
			$sOrder .= " ORDER BY {$aFilter['Order']['By']} {$aFilter['Order']['Direction']}";
		return $sOrder;
	}
	
	//**************************************************************************************************
	protected function BuildLimitStatement($aFilter){
		$sLimit = "";
		if(isset($aFilter['Limit']))
			$sLimit .= " LIMIT {$aFilter['Limit']}";
		return $sLimit;
	}
	
	//**************************************************************************************************
	protected function EntityByTableRow($sEntity, $aRow){
		if($aRow){
			$sEntityFull	= "ModuleFeedbacks_Entity".$sEntity;
			return Engine::GetEntity($sEntityFull, $aRow);
		}else return false;
	}
	
	//**************************************************************************************************
	protected function EntityArrayByQueryResult($sEntity, $aQueryResult){
		$aResult	= array();
		if($aQueryResult){
			foreach($aQueryResult as $aRow){
				$aResult[]	= $this->EntityByTableRow($sEntity, $aRow);
			}
		}
		return $aResult;	
	}

	//**************************************************************************************************
	public function SaveAction($oAction){

		$sTableName	= $this->TableName('actions');
		$sQuery		= "INSERT INTO {$sTableName}
							(id, user_id_to, user_id_from, action_type, add_datetime, destination_object_id, action_object_id)
						VALUES
							(?d, ?d, ?d, ?, ?d, ?d, ?d)
						ON DUPLICATE KEY UPDATE
							user_id_to 		= ?d,
							user_id_from	= ?d,
							action_type		= ?,
							add_datetime	= ?d,

							destination_object_id		= ?d,
							action_object_id			= ?d
						";

		return $this->oDb->Query($sQuery,
									$oAction->getId(), $oAction->getUserIdTo(), $oAction->getUserIdFrom(), $oAction->getActionType(),
										$oAction->getAddDatetime(), $oAction->getDestinationObjectId(), $oAction->getActionObjectId(),

									$oAction->getUserIdTo(), $oAction->getUserIdFrom(), $oAction->getActionType(),
										$oAction->getAddDatetime(), $oAction->getDestinationObjectId(), $oAction->getActionObjectId()
		);

	}

	//**************************************************************************************************
	public function GetActionsByUserId($iUserId, $iActionsCount){
		$sTable		= $this->TableName('actions');
		$sQuery		= "SELECT * FROM {$sTable} WHERE user_id_to = ?d ORDER BY add_datetime DESC LIMIT ?d";

		$aResult	= $this->oDb->Select($sQuery, $iUserId, $iActionsCount);

		return $this->EntityArrayByQueryResult('Action', $aResult);
	}

	//**************************************************************************************************
	public function UpdateViewDatetimeByUserId($iUserId){
		$sTable		= $this->TableName('views');
		$sQuery		= "INSERT INTO {$sTable} (user_id, view_datetime) VALUES (?d, ?d)
						ON DUPLICATE KEY UPDATE view_datetime = ?d";

		return $this->oDb->Query($sQuery, $iUserId, time(), time());
	}

	//**************************************************************************************************
	public function GetUnreadItemsCountByUserId($iUserId){
		$sViews		= $this->TableName('views');
		$sActions	= $this->TableName('actions');

		$sQuery	= "SELECT COUNT(id) AS Count, user_id_to, add_datetime FROM {$sActions}
						WHERE add_datetime > (SELECT view_datetime FROM {$sViews} WHERE user_id = ?d)
						AND user_id_to = ?d";

		$aResult	= $this->oDb->SelectRow($sQuery, $iUserId, $iUserId);

		if($aResult){
			return $aResult['Count'];
		}else return 0;
	}

	//**************************************************************************************************
	public function GetActionsByUserIdLastActionId($iUserId, $iLastActionId, $iActionsCount){
		$sTable		= $this->TableName('actions');
		$sQuery		= "SELECT * FROM {$sTable} WHERE user_id_to = ?d AND id < ?d ORDER BY add_datetime DESC LIMIT ?d";

		$aResult	= $this->oDb->Select($sQuery, $iUserId, $iLastActionId, $iActionsCount);

		return $this->EntityArrayByQueryResult('Action', $aResult);
	}


}

?>
