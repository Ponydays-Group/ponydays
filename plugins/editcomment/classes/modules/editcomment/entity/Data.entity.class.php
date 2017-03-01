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


class PluginEditcomment_ModuleEditcomment_EntityData extends EntityORM
{

    protected $aRelations=array(
'comment'=>array('belongs_to', 'ModuleComment_EntityComment', 'comment_id'),
'user'=>array('belongs_to', 'ModuleUser_EntityUser', 'user_id'),
'previous_edit'=>array('belongs_to', 'PluginEditcomment_ModuleEditcomment_EntityData', 'previous_id'),
);

}
?>
