<?php

namespace solo\swarp\event;

use pocketmine\Player;
use pocketmine\level\Position;
use pocketmine\event\Cancellable;

use solo\swarp\Warp;

class PlayerWarpEvent extends WarpEvent implements Cancellable{

    public static $handlerList = null;

    protected $player;

    protected $destination;

    public function __construct(Warp $warp, Player $player, Position $destination){
        parent::__construct($warp);
        $this->player = $player;
        $this->destination = $destination;
    }

    public function getWarp() : Warp{
        return $this->warp;
    }

    public function getPlayer() : Player{
        return $this->player;
    }

    public function getDestination() : Position{
        return $this->destination;
    }

    public function setDestination(Position $destination){
        $this->destination = $destination;
    }
}
