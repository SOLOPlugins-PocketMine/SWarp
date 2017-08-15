<?php

namespace solo\swarp;

use pocketmine\Player;
use pocketmine\level\Position;

class WarpEvent{

  /** @var Warp */
  protected $warp;

  /** @var Player */
  protected $player;

  /** @var Position */
  protected $destination;

  public function __construct(Warp $warp, Player $player, Position $destination){
    $this->warp = $warp;
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
