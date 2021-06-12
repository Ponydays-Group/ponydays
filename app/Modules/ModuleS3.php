<?php

namespace App\Modules;

use Engine\Config;
use Engine\Module;

class ModuleS3 extends Module
{
    private $s3Client;

    public function Init() {
        $this->s3Client = (new \Aws\Sdk)->createMultiRegionS3([
            'version' => 'latest',
            'credentials' => [
                'key' => Config::Get('aws.key'),
                'secret' => Config::Get('aws.secret')
            ]
        ]);
    }

    public function UploadImage($sKey, $sFileName) {
        $this->s3Client->putObject([
            'Bucket' => Config::Get('aws.bucket'),
            'Key' =>  $sKey,
            'SourceFile' => $sFileName
        ]);
    }
}
