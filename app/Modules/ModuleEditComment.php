<?php
/*-------------------------------------------------------
*
*   kEditComment.
*   Copyright © 2012 Alexei Lukin
*
*--------------------------------------------------------
*
*   Official site: http://kerbystudio.ru
*   Contact e-mail: kerby@kerbystudio.ru
*
---------------------------------------------------------
*/

namespace App\Modules;

use App\Entities\EntityEditCommentData;
use App\Mappers\MapperEditComment;
use Engine\Engine;
use Engine\Module;

class ModuleEditComment extends Module
{
    /** @var \App\Mappers\MapperEditComment */
    protected $oMapper;

    /**
     * Инициализация модуля
     */
    public function Init()
    {
        $this->oMapper=Engine::MakeMapper(MapperEditComment::class);
    }

    /**
     * @param $iCommentId
     *
     * @return \App\Entities\EntityEditCommentData
     */
    public function GetLastEditData($iCommentId)
    {
        $data = $this->oMapper->GetLastEditData($iCommentId);
        return $data;
    }
    /**
     * @param $iCommentId
     *
     * @return array ModuleEditComment_EntityData
     */
    public function GetDataItemsByCommentId($iCommentId)
    {
        $iCount = 0;
        return $this->oMapper->GetDataItemsByCommentId($iCommentId, $iCount);
    }
    
    public function HasAnswers($sId)
    {
        return $this->oMapper->HasAnswers($sId);
    }

    public function GetFirstAnswer($sId)
    {
        return $this->oMapper->GetFirstAnswer($sId);
    }

    /**
     * @param $data \App\Entities\EntityEditCommentData
     *
     * @return bool
     */
    public function SaveData($data) {
        return $this->oMapper->SaveData($data);
    }
}
