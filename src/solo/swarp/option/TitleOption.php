<?php

namespace solo\swarp\option;

use solo\swarp\SWarp;
use solo\swarp\WarpException;
use solo\swarp\WarpOption;
use solo\swarp\event\PlayerWarpEvent;
use solo\swarp\option\argument\ArgumentString;

class TitleOption extends WarpOption{

  /** @var string */
  private $titleMessage;

  public function __construct(ArgumentString ...$args){
    $this->titleMessage = implode(" ", $args);
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

  protected function dataSerialize() : array{
    return [
      "titleMessage" => $this->titleMessage
    ];
  }

  protected function dataDeserialize(array $data) : void{
    $this->titleMessage = $data["titleMessage"];
  }
}
