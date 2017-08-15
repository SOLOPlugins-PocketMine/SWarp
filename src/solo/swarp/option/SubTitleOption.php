<?php

namespace solo\swarp\option;

use solo\swarp\SWarp;
use solo\swarp\WarpEvent;
use solo\swarp\WarpException;
use solo\swarp\WarpOption;

class SubTitleOption extends WarpOption{

  private $subTitleMessage;

  public function __construct(string $value = ""){
    $this->subTitleMessage = $value;
  }

  public function getName() : string{
    return "서브타이틀";
  }

  public function apply(WarpEvent $event){
    SWarp::getInstance()->getTitleManager()->addSubTitle($event->getPlayer(), $this->subTitleMessage);
  }

  public function __toString(){
    return $this->getName() . " : " . $this->subTitleMessage;
  }

  public function yamlSerialize(){
    $data = parent::yamlSerialize();
    $data["subTitleMessage"] = $this->subTitleMessage;
    return $data;
  }

  public static function yamlDeserialize(array $data){
    $option = parent::yamlDeserialize($data);
    $option->subTitleMessage = $data["subTitleMessage"];
    return $option;
  }
}
