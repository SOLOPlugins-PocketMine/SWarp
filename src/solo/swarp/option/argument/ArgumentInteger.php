<?php

namespace solo\swarp\option\argument;

class ArgumentInteger extends Argument{

  public function setValue($value){
    if(!preg_match("/^\-?[0-9]+$/", $value)){
      throw new \InvalidArgumentException("값은 정수이어야 합니다.");
    }
    parent::setValue(intval($value));
  }
}
