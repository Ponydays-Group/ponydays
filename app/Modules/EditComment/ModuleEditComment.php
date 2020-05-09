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

namespace App\Modules\EditComment;

use App\Modules\EditComment\Entity\ModuleEditComment_EntityData;
use App\Modules\EditComment\Mapper\ModuleEditComment_MapperEditComment;
use Engine\Engine;
use Engine\Module;

class ModuleEditComment extends Module
{
    /** @var ModuleEditComment_MapperEditComment */
    protected $oMapper;

    /**
     * Инициализация модуля
     */
    public function Init()
    {
        $this->oMapper=Engine::MakeMapper(ModuleEditComment_MapperEditComment::class);
    }

    /**
     * @param $iCommentId
     *
     * @return ModuleEditComment_EntityData
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
     * @param $data ModuleEditComment_EntityData
     *
     * @return bool
     */
    public function SaveData($data) {
        return $this->oMapper->SaveData($data);
    }
}
