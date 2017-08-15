<?php

namespace solo\swarp;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\scheduler\PluginTask;

interface ISWarpTask{

  public function _onRun(int $currentTick);

}

if(Server::getInstance()->getName() === "PocketMine-MP" && version_compare(\PocketMine\API_VERSION, "3.0.0-ALPHA7") >= 0){
  abstract class SWarpTask extends PluginTask implements ISWarpTask{
    public function onRun(int $currentTick){
      return $this->_onRun($currentTick);
    }
  }
}else{
  abstract class SWarpTask extends PluginTask implements ISWarpTask{
    public function onRun($currentTick){
      return $this->_onRun($currentTick);
    }
  }
}
