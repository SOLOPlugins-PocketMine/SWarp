<?php

namespace solo\swarp\event;

use pocketmine\event\Event;
use solo\swarp\Warp;

abstract class WarpEvent extends Event{

  protected $warp;

  public function __construct(Warp $warp){
    $this->warp = $warp;
  }

  public function getWarp(){
    return $this->warp;
  }
}
