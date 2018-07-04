<?php

namespace solo\swarp;

use solo\swarp\event\PlayerWarpEvent;

abstract class WarpOption{

    abstract public function getName() : string;

    public function test(PlayerWarpEvent $event){

    }

    public function apply(PlayerWarpEvent $event){

    }

    final public function jsonSerialize() : array{
        return $this->dataSerialize();
    }

    final public static function jsonDeserialize(array $data) : WarpOption{
        $option = static::createObject();
        $option->dataDeserialize($data);
        return $option;
    }

    protected function dataSerialize() : array{
        return [];
    }

    protected function dataDeserialize(array $data) : void{

    }

    protected static function createObject() : WarpOption{
        return (new \ReflectionClass(static::class))->newInstanceWithoutConstructor();
    }
}
