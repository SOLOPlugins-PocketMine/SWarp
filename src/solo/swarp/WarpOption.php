<?php

namespace solo\swarp;

use solo\swarp\event\PlayerWarpEvent;

abstract class WarpOption{

  abstract public function getName() : string;

  public function test(PlayerWarpEvent $event){

  }

  public function apply(PlayerWarpEvent $event){

  }

  public function jsonSerialize() : array{
    return get_object_vars($this);
  }

  public static function jsonDeserialize(array $data) : WarpOption{
    $ref = new \ReflectionClass(static::class);
    $option = $ref->newInstanceWithoutConstructor();
    foreach($data as $key => $value){
      $option->{$key} = $value;
    }
    return $option;
  }
}
