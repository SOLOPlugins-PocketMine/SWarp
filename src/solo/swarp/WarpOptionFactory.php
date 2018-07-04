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

abstract class WarpOptionFactory{

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
                self::registerWarpOption(GainMoney::class);
            }
        }catch(\Throwable $e){

        }
        self::registerWarpOption(CooldownOption::class);
        self::registerWarpOption(DamageOption::class);
        self::registerWarpOption(HealOption::class);
        self::registerWarpOption(EffectOption::class);
        self::registerWarpOption(GainItemOption::class);
        self::registerWarpOption(ConsumeItemOption::class);
        self::registerWarpOption(GainMoneyOption::class);
    }

    private static function parseString(string $input) : array{
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
                $options[$optionName] = $optionArgs;
            }
            ++$index;
        }
        return $options;
    }

    public static function parseOptions(string $input) : array{
        $options = [];
        foreach(self::parseString($input) as $name => $args){
            $class = self::getWarpOption($name);
            if($class === null){
                throw new \InvalidArgumentException("\"" . $name . "\" 옵션은 존재하지 않습니다.");
            }

            $parameters = (new \ReflectionClass($class))->getConstructor()->getParameters();
            $arguments = [];

            $usage = "사용법 : -" . $name . " " . implode(" ", array_map(function($parameter){
                if($parameter->isOptional()){
                    return "[" . $parameter->getName() . "]";
                }else{
                    return "<" . $parameter->getName() . ">";
                }
            }, $parameters));

            $parameter = null;
            while(!empty($parameters) || !empty($args)){
                if($parameter === null || !$parameter->isVariadic()){
                    $parameter = array_shift($parameters);
                }
                if($parameter === null){
                    throw new \InvalidArgumentException($name . ": 입력 값이 너무 많습니다. " . $usage);
                }

                $arg = array_shift($args);
                if($arg === null){
                    if(!$parameter->isOptional() || ($parameter->isVariadic() && empty($arguments))){
                        throw new \InvalidArgumentException($name . ": 입력 값이 부족합니다. " . $usage);
                    }
                }

                $argumentClass = $parameter->getClass()->getName();
                try{
                    $arguments[] = new $argumentClass($arg);
                }catch(\InvalidArgumentException $e){
                    throw new \InvalidArgumentException($name . ": " . $parameter->getName() . " " . $e->getMessage() . " " . $usage);
                }
            }

            try{
                $option = new $class(...$arguments);
            }catch(\InvalidArgumentException $e){
                throw new \InvalidArgumentException($optionName . ": " . $e->getMessage());
            }
            $options[$option->getName()] = $option;
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
