<?php

namespace solo\swarp;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class SWarp extends PluginBase{

  private static $instance = null;

  public static $prefix = "§b§l[SWarp] §r§7";

  public static function getInstance() : SWarp{
    if(self::$instance === null){
      throw new \InvalidStateException();
    }
    return self::$instance;
  }



  /** @var Config */
  private $setting;

  /** @var WarpOptionFactory */
  private $warpOptionFactory;

  /** @var TitleManager */
  private $titleManager;

  /** @var ShortcutManager */
  private $shortcutManager;

  /** @var Config */
  private $warpsConfig;

  /** @var Warp[] */
  private $warps = null;

  public function onLoad(){
    if(self::$instance !== null){
      throw new \InvalidStateException();
    }
    self::$instance = $this;
  }

  public function onEnable(){
    @mkdir($this->getDataFolder());
    $this->saveResource("setting.yml");
    $this->setting = new Config($this->getDataFolder() . "setting.yml", Config::YAML);

    $this->warpOptionFactory = new WarpOptionFactory($this);

    $this->shortcutManager = new ShortcutManager($this);

    $this->titleManager = new TitleManager($this);

    $this->load();

    foreach([
      "WarpCommand",
      "WarpCreateCommand",
      "WarpDescriptionCommand",
      "WarpInfoCommand",
      "WarpListCommand",
      "WarpOptionCommand",
      "WarpPermissionCommand",
      "WarpRemoveCommand",
      "WorldMoveCommand"
    ] as $class){
      $class = "\\solo\\swarp\\command\\" . $class;
      $this->getServer()->getCommandMap()->register("swarp", new $class($this));
    }
  }

  public function onDisable(){
    $this->save();

    self::$instance = null;
  }

  public function getWarpOptionFactory(){
    return $this->warpOptionFactory;
  }

  public function getTitleManager(){
    return $this->titleManager;
  }

  public function getShortcutManager(){
    return $this->shortcutManager;
  }


  public function addWarp(Warp $warp){
    $this->warps[strtolower($warp->getName())] = $warp;
  }

  public function getWarp(string $name){
    return $this->warps[strtolower($name)] ?? null;
  }

  public function getAllWarp(){
    return $this->warps;
  }

  public function removeWarp($warp){
    if($warp instanceof Warp){
      $warp = $warp->getName();
    }
    unset($this->warps[strtolower($warp)]);
  }

  public function save(){
    if($this->warps === null){
      return;
    }

    $serializedData = [];
    foreach($this->warps as $warp){
      $data = $warp->yamlSerialize();
      $data["class"] = get_class($warp);
      $serializedData[] = $data;
    }
    $this->warpsConfig->setAll($serializedData);
    $this->warpsConfig->save();
  }

  public function load(){
    $this->warpsConfig = new Config($this->getDataFolder() . "warps.yml", Config::YAML);

    $warps = [];

    foreach($this->warpsConfig->getAll() as $data){
      $class = $data["class"];
      unset($data["class"]);

      if(!class_exists($class, true)){
        $this->getServer()->getLogger()->critical("[SWarp] " . $class . " 클래스를 찾을 수 없습니다.");
        continue;
      }else if($class !== Warp::class && !is_subclass_of($class, Warp::class)){
        $this->getServer()->getLogger()->critical("[SWarp] " . $class . " 클래스는 " . Warp::class . " 의 서브클래스가 아닙니다.");
        continue;
      }
      $warp = $class::yamlDeserialize($data);

      $warps[strtolower($warp->getName())] = $warp;
    }
    $this->warps = $warps;
  }
}
