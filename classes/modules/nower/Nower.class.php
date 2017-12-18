<?php

class ModuleNower extends Module {
    public function Init() {}

    public function Post($sUrl, $aData=array()) {
        $myCurl = curl_init();
        curl_setopt_array($myCurl, array(
            CURLOPT_URL => 'http://127.0.0.1:3000'.$sUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($aData)
        ));
        curl_exec($myCurl);
        curl_close($myCurl);
    }

    public function Get($sUrl, $aData=array()) {
        $myCurl = curl_init();
        curl_setopt_array($myCurl, array(
            CURLOPT_URL => Config::Get("nower_url").$sUrl."?".http_build_query($aData),
            CURLOPT_RETURNTRANSFER => true,
        ));
        curl_exec($myCurl);
        curl_close($myCurl);
    }

    public function Delete($sUrl, $aData=array()) {
        $myCurl = curl_init();
        curl_setopt_array($myCurl, array(
            CURLOPT_URL => Config::Get("nower_url").$sUrl."?".http_build_query($aData),
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => http_build_query($aData)
        ));
        curl_exec($myCurl);
        curl_close($myCurl);
    }

    public function Patch($sUrl, $aData=array()) {
        $myCurl = curl_init();
        curl_setopt_array($myCurl, array(
            CURLOPT_URL => Config::Get("nower_url").$sUrl."?".http_build_query($aData),
            CURLOPT_CUSTOMREQUEST => "PATCH",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => http_build_query($aData)
        ));
        curl_exec($myCurl);
        curl_close($myCurl);
    }
}