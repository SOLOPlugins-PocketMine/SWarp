<?php

namespace solo\swarp;

use pocketmine\utils\Config;
use solo\swarp\event\WarpCreateEvent;
use solo\swarp\event\WarpRemoveEvent;

class WarpManager{

  /** @var SWarp */
  private $owner;

  /** @var Config */
  private $warpsConfig;

  /** @var Warp[] */
  private $warps = [];

  public function __construct(SWarp $owner){
    $this->owner = $owner;

    $this->load();
  }

  private function load(){
    $this->warpsConfig = new Config($this->owner->getDataFolder() . "warps.yml", Config::YAML);
    $warps = [];

    foreach($this->warpsConfig->getAll() as $data){
      $class = $data["class"];
      unset($data["class"]);

      if(!class_exists($class, true)){
        $this->owner->getServer()->getLogger()->critical("[SWarp] " . $class . " 클래스를 찾을 수 없습니다.");
        continue;
      }else if($class !== Warp::class && !is_subclass_of($class, Warp::class)){
        $this->owner->getServer()->getLogger()->critical("[SWarp] " . $class . " 클래스는 " . Warp::class . " 의 서브클래스가 아닙니다.");
        continue;
      }
      $warp = $class::jsonDeserialize($data);

      $warps[strtolower($warp->getName())] = $warp;
    }
    $this->warps = $warps;
  }

  public function save(){
    if(empty($this->warps) || !$this->warpsConfig instanceof Config){
      return;
    }
    $serializedData = [];
    foreach($this->warps as $warp){
      $data = $warp->jsonSerialize();
      $data["class"] = get_class($warp);
      $serializedData[] = $data;
    }
    $this->warpsConfig->setAll($serializedData);
    $this->warpsConfig->save();
  }

  public function addWarp(Warp $warp) : Warp{
    if(isset($this->warps[$name = strtolower($warp->getName())])){
      throw new WarpAlreadyExistsException("\"" . $name . "\" 이름의 워프는 이미 존재합니다");
    }
    $this->owner->getServer()->getPluginManager()->callEvent($ev = new WarpCreateEvent($warp));
    if($ev->isCancelled()){
      throw new WarpException("워프 생성에 실패하였습니다");
    }
    return $this->warps[strtolower($warp->getName())] = $warp;
  }

  public function getWarp(string $name) : ?Warp{
    return $this->warps[strtolower($name)] ?? null;
  }

  public function getAllWarp() : array{
    return $this->warps;
  }

  public function removeWarp($warp) : Warp{
    if($warp instanceof Warp){
      $warp = $warp->getName();
    }
    $warp = strtolower($warp);

    if(!isset($this->warps[$warp])){
      throw new WarpNotExistsException("\"" . $warp . "\" 이름의 워프는 존재하지 않습니다");
    }
    $warpInstance = $this->warps[$warp];

    $ev = new WarpRemoveEvent($warpInstance);
    $this->owner->getServer()->getPluginManager()->callEvent($ev = new WarpRemoveEvent($warp));
    if($ev->isCancelled()){
      throw new WarpException("워프 제거에 실패하였습니다");
    }

    unset($this->warps[strtolower($warp)]);
    return $warpInstance;
  }
}
