<?php

namespace solo\swarp\event;

class WarpOptionUpdateEvent extends WarpEvent{

  public static $handlerList = null;

  public function getOptions(){
    return $this->warp->getOptions();
  }

  public function getOption(string $name){
    return $this->warp->getOption($name);
  }

  public function setOptions(array $options){
    $this->warp->setOptions($options);
  }
}
