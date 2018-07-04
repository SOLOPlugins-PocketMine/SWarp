<?php

namespace solo\swarp\command;

use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\level\Level;
use solo\swarp\SWarp;

class WorldMoveCommand extends Command{

    private $owner;

    public function __construct(SWarp $owner){
        parent::__construct("월드이동", "다른 월드로 이동합니다.", "/월드이동 <월드명>");
        $this->setPermission("swarp.command.worldmove");

        $this->owner = $owner;
    }

    public function execute(CommandSender $sender, string $label, array $args) : bool{
        if(!$sender->hasPermission($this->getPermission())){
            $sender->sendMessage(SWarp::$prefix . "이 명령을 실행할 권한이 없습니다.");
            return true;
        }

        if(!$sender instanceof Player){
            $sender->sendMessage(SWarp::$prefix . "인게임에서만 사용할 수 있습니다.");
            return true;
        }

        if(empty($args)){
            $sender->sendMessage(SWarp::$prefix . "사용법 : " . $this->getUsage() . " - " . $this->getDescription());
            return true;
        }

        $levelName = implode(" ", $args);
        $level = $this->owner->getServer()->getLevelByName($levelName);

        if(!$level instanceof Level){
            $sender->sendMessage(SWarp::$prefix . "\"" . $levelName . "\" 월드는 존재하지 않습니다.");
            return true;
        }

        $sender->teleport($level->getSpawnLocation());
        $sender->sendMessage(SWarp::$prefix . $level->getName() . " 월드로 이동하였습니다.");
        return true;
    }
}
