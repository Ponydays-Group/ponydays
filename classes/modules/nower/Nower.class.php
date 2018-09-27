<?php

class ModuleNower extends Module {
    public function Init() {}

    public function PostNotification($aData=array()) {
    	$aData = $aData->getArrayData();
        $myCurl = curl_init();
        curl_setopt_array($myCurl, array(
            CURLOPT_URL => 'http://127.0.0.1:3000/notification',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => http_build_query($aData)
		));
        curl_exec($myCurl);
        curl_close($myCurl);
    }
}