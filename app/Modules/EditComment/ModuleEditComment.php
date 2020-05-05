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

use App\Modules\EditComment\Mapper\ModuleEditComment_MapperEditComment;
use Engine\Engine;
use Engine\LS;
use Engine\ModuleORM;

class ModuleEditComment extends ModuleORM
{

    protected $oMapper;

    /**
     * Инициализация модуля
     */
    public function Init()
    {
        parent::Init();
        $this->oMapper=Engine::MakeMapper(ModuleEditComment_MapperEditComment::class);
    }
    
    public function GetLastEditData($iCommentId)
    {
        $arr=LS::Make(ModuleEditComment::class)->GetDataItemsByFilter(array('comment_id'=>$iCommentId, '#order'=>array('date_add'=>'desc'), '#limit'=>array(0, 1)));
        return array_pop($arr);
    }    
    
    public function HasAnswers($sId)
    {
        return $this->oMapper->HasAnswers($sId);
    }

    public function GetFirstAnswer($sId)
    {
        return $this->oMapper->GetFirstAnswer($sId);
    }

}
