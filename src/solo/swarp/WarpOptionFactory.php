<?php

namespace solo\swarp;

use solo\swarp\option\CostOption;
use solo\swarp\option\CooldownOption;
use solo\swarp\option\DepartureParticleOption;
use solo\swarp\option\DescriptionOption;
use solo\swarp\option\DestinationParticleOption;
use solo\swarp\option\RandomDestinationOption;
use solo\swarp\option\ShortcutOption;
use solo\swarp\option\SubTitleOption;
use solo\swarp\option\TitleOption;
use solo\swarp\option\DamageOption;
use solo\swarp\option\HealOption;

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
  }

  public static function parseOptions(string $input) : array{
    $args = explode(" ", $input);

    $options = [];

    $i = 0;
    while($i < count($args)){
      if(substr($args[$i], 0, 1) == "-"){
        $optionName = substr($args[$i], 1);
        $optionValueArgs = [];
        while(substr($args[$i + 1] ?? "-", 0, 1) != "-"){
          $optionValueArgs[] = $args[++$i];
        }
        $optionValue = implode(" ", $optionValueArgs);

        $optionClass = self::getWarpOption($optionName);
        if($optionClass === null || !class_exists($optionClass, true)){
          throw new \InvalidArgumentException($optionName . " 옵션은 존재하지 않습니다.");
        }

        $optionInstance = new $optionClass($optionValue);
        $options[$optionInstance->getName()] = $optionInstance;
      }
      $i++;
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
    self::warpOptionClasses[$obj->getName()] = $class;
  }
}
