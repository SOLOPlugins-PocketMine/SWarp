<?php

namespace solo\swarp;

abstract class WarpOption{

  abstract public function getName() : string;

  public function test(WarpEvent $event){

  }

  abstract public function apply(WarpEvent $event);

  public function yamlSerialize(){
    return [];
  }

  public static function yamlDeserialize(array $data){
    $ref = new \ReflectionClass(static::class);
    $option = $ref->newInstanceWithoutConstructor();
    return $option;
  }
}
