<?php

namespace solo\swarp;

use solo\swarp\event\PlayerWarpEvent;

abstract class WarpOption{

  abstract public function getName() : string;

  public function test(PlayerWarpEvent $event){

  }

  public function apply(PlayerWarpEvent $event){

  }

  public function yamlSerialize(){
    return [];
  }

  public static function yamlDeserialize(array $data){
    $ref = new \ReflectionClass(static::class);
    $option = $ref->newInstanceWithoutConstructor();
    return $option;
  }
}
