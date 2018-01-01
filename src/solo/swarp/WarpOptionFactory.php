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

  /** @var SWarp */
  private $owner;

  private $warpOptionClasses = [];

  public function __construct(SWarp $owner){
    $this->owner = $owner;

    $this->init();
  }

  private function init(){
    $this->registerWarpOption(TitleOption::class);
    $this->registerWarpOption(SubTitleOption::class);
    $this->registerWarpOption(DepartureParticleOption::class);
    $this->registerWarpOption(DestinationParticleOption::class);
    $this->registerWarpOption(RandomDestinationOption::class);
    try{
      if(class_exists("\\onebone\\economyapi\\EconomyAPI")){
        $this->registerWarpOption(CostOption::class);
      }
    }
    $this->registerWarpOption(CooldownOption::class);
    $this->registerWarpOption(DamageOption::class);
    $this->registerWarpOption(HealOption::class);
  }

  public function parseOptions(string $input) : array{
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

        $optionClass = $this->getWarpOption($optionName);
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

  public function getWarpOption(string $name){
    return $this->warpOptionClasses[$name] ?? null;
  }

  public function getAllWarpOptions() : array{
    return $this->warpOptionClasses;
  }

  public function registerWarpOption($class){
    $ref = new \ReflectionClass($class);
    $obj = $ref->newInstanceWithoutConstructor();
    $this->warpOptionClasses[$obj->getName()] = $class;
  }
}
