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
    return [];
  }

  public static function jsonDeserialize(array $data) : WarpOption{
    $ref = new \ReflectionClass(static::class);
    $option = $ref->newInstanceWithoutConstructor();
    return $option;
  }
}
