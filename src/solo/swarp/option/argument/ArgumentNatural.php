<?php

namespace solo\swarp\option\argument;

class ArgumentNatural extends Argument{

    public function setValue($value){
        if(!preg_match("/^[0-9]+$/", $value) || intval($value) <= 0){
            throw new \InvalidArgumentException("값은 자연수이어야 합니다.");
        }
        parent::setValue(intval($value));
    }
}
