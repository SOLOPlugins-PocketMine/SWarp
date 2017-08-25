<?php

namespace solo\swarp\event;

use solo\swarp\SWarpEvent;
use solo\swarp\Warp;

abstract class WarpEvent extends SWarpEvent{

  protected $warp;

  public function __construct(Warp $warp){
    $this->warp = $warp;
  }

  public function getWarp(){
    return $this->warp;
  }
}
