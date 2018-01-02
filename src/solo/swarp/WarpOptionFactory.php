<?php

namespace solo\swarp;

use solo\swarp\option\ConsumeItemOption;
use solo\swarp\option\CooldownOption;
use solo\swarp\option\CostOption;
use solo\swarp\option\DamageOption;
use solo\swarp\option\DepartureParticleOption;
use solo\swarp\option\DestinationParticleOption;
use solo\swarp\option\EffectOption;
use solo\swarp\option\GainItemOption;
use solo\swarp\option\GainMoneyOption;
use solo\swarp\option\HealOption;
use solo\swarp\option\RandomDestinationOption;
use solo\swarp\option\SubTitleOption;
use solo\swarp\option\TitleOption;

class WarpOptionFactory{

  private function __construct(){

  }

  private static $warpOptionClasses = [];

  public static function init(){
    self::registerWarpOption(TitleOption::class);
    self::registerWarpOption(SubTitleOption::class);
    self::registerWarpOption(DepartureParticleOption::class);
    self::registerWarpOption(DestinationParticleOption::class);
    self::registerWarpOption(RandomDestinationOption::class);
    try{
      if(class_exists("\\onebone\\economyapi\\EconomyAPI")){
        self::registerWarpOption(CostOption::class);
      }
    }catch(\Throwable $e){

    }
    self::registerWarpOption(CooldownOption::class);
    self::registerWarpOption(DamageOption::class);
    self::registerWarpOption(HealOption::class);
    self::registerWarpOption(EffectOption::class);
    self::registerWarpOption(GiveItemOption::class);
  }

  public static function parseOptions(string $input) : array{
    $args = explode(" ", $input);

    $options = [];

    $index = 0;
    while($index < count($args)){
      if(substr($args[$index], 0, 1) == "-"){
        $optionName = substr($args[$index], 1);
        $optionArgs = [];
        while(substr($args[$index + 1] ?? "-", 0, 1) != "-"){
          $optionArgs[] = $args[++$index];
        }

        $optionClass = self::getWarpOption($optionName);
        if($optionClass === null || !class_exists($optionClass, true)){
          throw new \InvalidArgumentException("\"" . $optionName . "\" 옵션은 존재하지 않습니다.");
        }

        $constructor = (new \ReflectionClass($optionClass))->getConstructor();
        $parameters = $constructor->getParameters();
        $optionInvokeArgs = [];

        $usage = "사용법 : -" . $optionName . " " . implode(" ", array_map(function($parameter){
          if($parameter->isOptional()){
            return "[" . $parameter->getName() . "]";
          }else{
            return "<" . $parameter->getName() . ">";
          }
        }, $parameters));

        while(true){
          // Parameters iterate
          if(!isset($parameter) || !$parameter->isVariadic()){
            $parameter = array_shift($parameters);
            if($parameter === null){
              break;
            }
          }

          // optionArgs iterate
          $argInput = array_shift($optionArgs);

          if($argInput === null){
            if($parameter->isVariadic() && count($optionInvokeArgs) > 0){
              throw new \InvalidArgumentException($optionName . ": 입력 값이 부족합니다. " . $usage);
            }
            if($parameter->isOptional()){
              continue;
            }
            throw new \InvalidArgumentException($optionName . ": 입력 값이 부족합니다. " . $usage);
          }
          $argClass = $parameter->getClass()->getName();

          try{
            $argObject = new $argClass($argInput);
          }catch(\InvalidArgumentException $e){
            throw new \InvalidArgumentException($optionName . ": " . $parameter->getName() . " " . $e->getMessage() . " " . $usage);
          }

          $optionInvokeArgs[] = $argObject;
        }

        try{
          $optionInstance = new $optionClass(...$optionInvokeArgs);
        }catch(\InvalidArgumentException $e){
          throw new \InvalidArgumentException($optionName . ": " . $e->getMessage());
        }
        $options[$optionInstance->getName()] = $optionInstance;
      }
      $index++;
    }
    return $options;
  }

  public static function getWarpOption(string $name){
    return self::$warpOptionClasses[$name] ?? null;
  }

  public static function getAllWarpOptions() : array{
    return self::$warpOptionClasses;
  }

  public static function registerWarpOption($class){
    $obj = (new \ReflectionClass($class))->newInstanceWithoutConstructor();
    self::$warpOptionClasses[$obj->getName()] = $class;
  }
}
