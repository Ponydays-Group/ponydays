<?php

namespace App\Modules;

use App\Mappers\MapperAPI;
use Engine\Engine;
use Engine\Module;

class ModuleAPI extends Module
{
    protected $oMapper;

    public function Init()
    {
        $this->oMapper = Engine::MakeMapper(MapperAPI::class);
    }

    public function setKey($iId, $sKey)
    {
        if ($iResult = $this->oMapper->writeKey($iId, $sKey)) {
            return $sKey;
        } else {
            return false;
        }
    }

    public function getKey($iId)
    {
        if ($iResult = $this->oMapper->readKey($iId)) {
            return $iResult;
        } else {
            return false;
        }
    }

    public function deleteKey($sKey)
    {
        if ($iResult = $this->oMapper->deleteKey($sKey)) {
            return true;
        } else {
            return false;
        }
    }

    public function getUserByKey($sKey)
    {
        if ($iResult = $this->oMapper->GetUserByKey($sKey)) {
            return $iResult;
        } else {
            return false;
        }
    }
}
