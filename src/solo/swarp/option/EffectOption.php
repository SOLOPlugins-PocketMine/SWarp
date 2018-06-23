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

  protected function dataSerialize() : array{
    return [
      "effectId" => $this->effect->getId(),
      "effectAmplifier" => $this->effect->getAmplifier(),
      "effectDuration" => $this->effect->getDuration()
    ];
  }

  protected function dataDeserialize(array $data) : void{
    $this->effect = Effect::getEffect($data["effectId"]);
    if(isset($data["effectAmplifier"])){
      $this->effect->setAmplifier($data["effectAmplifier"]);
    }
    if(isset($data["effectDuration"])){
      $this->effect->setDuration($data["effectDuration"]);
    }
  }
}
