<?php

namespace solo\swarp\option;

use solo\swarp\SWarp;
use solo\swarp\WarpException;
use solo\swarp\WarpOption;
use solo\swarp\event\PlayerWarpEvent;
use solo\swarp\option\argument\ArgumentFloatPositive;
use solo\swarp\option\argument\ArgumentNatural;
use solo\swarp\option\argument\ArgumentString;
use pocketmine\entity\Effect;

class EffectOption extends WarpOption{

  /** @var Effect */
  private $effect;

  public function __construct(ArgumentString $effectNameOrId, ArgumentNatural $amplification = null, ArgumentFloatPositive $duration = null){
    $effect = Effect::getEffectByName($effectNameOrId->getValue());
    if($amplification !== null){
      $effect->setAmplifier($amplification->getValue());
    }
    if($duration !== null){
      $effect->setDuration($duration->getValue() * 20);
    }
    $this->effect = $effect;
  }

  public function getName() : string{
    return "이펙트";
  }

  public function apply(PlayerWarpEvent $event){
    $event->getPlayer()->addEffect(clone $this->effect);
    $event->getPlayer()->sendMessage(SWarp::$prefix . "이펙트 \"" . $this->effect->getName() . "\" 이(가) 적용되었습니다.");
  }

  public function __toString(){
    return $this->getName() . " : " . $this->effect->getName() . " (강도:" . $this->effect->getAmplifier() . ", 시간:" . ($this->effect->getDuration() / 20) . ")";
  }

  public function jsonSerialize() : array{
    $data = parent::jsonSerialize();
    $data["effectId"] = $this->effect->getId();
    $data["effectAmplifier"] = $this->effect->getAmplifier();
    $data["effectDuration"] = $this->effect->getDuration();
    return $data;
  }

  public static function jsonDeserialize(array $data) : WarpOption{
    $option = static::createObject();
    $option->effect = Effect::getEffect($data["effectId"]);
    if(isset($data["effectAmplifier"])){
      $option->effect->setAmplifier($data["effectAmplifier"]);
    }
    if(isset($data["effectDuration"])){
      $option->effect->setDuration($data["effectDuration"]);
    }
    return $option;
  }
}
