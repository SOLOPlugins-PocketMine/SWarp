<?php

namespace solo\swarp\option\argument;

abstract class Argument{

    protected $value;

    public function __construct($value){
        $this->setValue($value);
    }

    public function setValue($value){
        $this->value = $value;
    }

    public function getValue(){
        return $this->value;
    }

    public function __toString(){
        return $this->value;
    }
}
