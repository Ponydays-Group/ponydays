<?php

class PluginTalkbell_ModuleTalkbell_MapperTalkbell extends Mapper
{

    public function GetNewMessage($sUserId)
    {

        $sql = "SELECT
                    tu.talk_id, tu.user_id, t.talk_title, t.user_id as user_onwer_id
                FROM " . Config::Get('db.table.talk_user') . " as tu
                LEFT JOIN " . Config::Get('db.table.talk') . " as t
                ON tu.talk_id=t.talk_id
                WHERE
                tu.user_id = ?d
                AND
                    tu.date_last IS NULL
                AND
                    tu.talk_user_active=?d";
        $aTalkId = array();
        $aReturn = array();
        $aTalk = array();
        if ($aRows = $this->oDb->select($sql, $sUserId, ModuleTalk::TALK_USER_ACTIVE)) {
            foreach ($aRows as $aRow) {
                $aTalk[$aRow['talk_id']] = Engine::GetEntity('Talk', array('user_id' => $aRow['user_onwer_id'], 'talk_id' => $aRow['talk_id'], 'talk_title' => $aRow['talk_title']));
                $aTalkId[$aRow['talk_id']] = $aRow['talk_id'];
            }
        }

        $sql = "SELECT tu.talk_id, tu.user_id ,tu.comment_count_new, t.talk_title, t.user_id as user_onwer_id
                FROM " . Config::Get('db.table.talk_user') . " as tu
                LEFT JOIN " . Config::Get('db.table.talk') . " as t
                    ON tu.talk_id=t.talk_id
                WHERE tu.comment_count_new != '0' AND tu.user_id = ?d AND tu.talk_user_active=?d";

        $aComment = array();
        $aCommentId = array();
        if ($aRows = $this->oDb->select($sql, $sUserId, ModuleTalk::TALK_USER_ACTIVE)) {
            foreach ($aRows as $aRow) {
                $aComment[$aRow['talk_id']] = Engine::GetEntity('Talk', array('talk_id' => $aRow['talk_id'], 'comment_count_new' => $aRow['comment_count_new'], 'talk_title' => $aRow['talk_title']));
                $aCommentId[$aRow['talk_id']] = $aRow['comment_count_new'];
            }
        }
        $aReturn['aComment'] = $aComment;
        $aReturn['aTalk'] = $aTalk;
        $aReturn['aTalkId'] = $aTalkId;
        $aReturn['aCommentId'] = $aCommentId;
        return $aReturn;
    }

    public function GetUserTalkSerialise($sUserId)
    {
        $sql = "SELECT * FROM " . Config::Get('plugin.talkbell.table.talk_bell') . " WHERE user_id = ?d";
        if ($aRow = $this->oDb->selectRow($sql, $sUserId)) {
            return Engine::GetEntity('PluginTalkbell_Talkbell', $aRow);
        }
        return false;
    }

    public function AddUserTalkSerialise($sUserId, $st, $sc)
    {
        $sql = "INSERT INTO " . Config::Get('plugin.talkbell.table.talk_bell') . "
                (
                    user_id,
                    user_data_talk,
                    user_data_comment
                )
                VALUES(?d, ?, ?)";
        if ($iId = $this->oDb->query($sql, $sUserId, $st, $sc)) {
            return $iId;
        }
        return false;
    }

    public function UpdUserTalkSerialise($sUserId, $st, $sc)
    {
        $sql = "UPDATE " . Config::Get('plugin.talkbell.table.talk_bell') . "
                SET
                  user_data_talk = ?,
                  user_data_comment = ?,
                  date = ?
                WHERE user_id = ?d";
        if ($this->oDb->query($sql, $st, $sc, date("Y-m-d H:i:s"), $sUserId)) {
            return true;
        }
        return false;
    }

    public function UpdateUserTalkBell($sUserId, $sValue)
    {
        $sql = "UPDATE " . Config::Get('db.table.user') . "
                SET
                    user_settings_talk_bell = ?d
                WHERE user_id = ?d";
        if ($this->oDb->query($sql, $sValue, $sUserId)) {
            return true;
        }
        return false;
    }
}

?>
