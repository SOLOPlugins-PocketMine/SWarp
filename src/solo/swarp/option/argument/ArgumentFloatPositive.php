<?php

namespace solo\swarp\option\argument;

class ArgumentFloatPositive extends Argument{

  public function setValue($value){
    if(!is_numeric($value) || floatval($value) <= 0){
      throw new \InvalidArgumentException("값은 0보다 큰 숫자이어야 합니다.");
    }
    parent::setValue(floatval($value));
  }
}
