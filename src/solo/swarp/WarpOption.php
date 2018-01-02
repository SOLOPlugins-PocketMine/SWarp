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
    $option = static::createObject();
    foreach($data as $key => $value){
      $option->{$key} = $value;
    }
    return $option;
  }

  protected static function createObject() : WarpOption{
    return (new \ReflectionClass(static::class))->newInstanceWithoutConstructor();
  }
}
