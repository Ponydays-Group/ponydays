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


class ModuleEditcomment extends ModuleORM
{

    protected $oMapper;

    /**
     * Инициализация модуля
     */
    public function Init()
    {
        parent::Init();
        $this->oMapper=Engine::GetMapper(__CLASS__);
    }
    
    public function GetLastEditData($iCommentId)
    {
        $arr=$this->Editcomment_GetDataItemsByFilter(array('comment_id'=>$iCommentId, '#order'=>array('date_add'=>'desc'), '#limit'=>array(0, 1)));
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
?>
