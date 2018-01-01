<?php

namespace solo\swarp\option;

use solo\swarp\SWarp;
use solo\swarp\WarpException;
use solo\swarp\WarpOption;
use solo\swarp\event\PlayerWarpEvent;

class TitleOption extends WarpOption{

  private $titleMessage;

  public function __construct(string $value = ""){
    $this->titleMessage = $value;
  }

  public function getName() : string{
    return "타이틀";
  }

  public function apply(PlayerWarpEvent $event){
    SWarp::getInstance()->getTitleManager()->addTitle($event->getPlayer(), $this->titleMessage);
  }

  public function __toString(){
    return $this->getName() . " : " . $this->titleMessage;
  }

  public function jsonSerialize() : array{
    $data = parent::jsonSerialize();
    $data["titleMessage"] = $this->titleMessage;
    return $data;
  }

  public static function jsonDeserialize(array $data) : WarpOption{
    $option = parent::jsonDeserialize($data);
    $option->titleMessage = $data["titleMessage"];
    return $option;
  }
}
