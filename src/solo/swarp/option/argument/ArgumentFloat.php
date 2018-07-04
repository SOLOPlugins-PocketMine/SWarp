<?php

namespace solo\swarp\option\argument;

class ArgumentFloat extends Argument{

    public function setValue($value){
        if(!is_numeric($value)){
            throw new \InvalidArgumentException("값은 숫자이어야 합니다.");
        }
        parent::setValue(floatval($value));
    }
}
