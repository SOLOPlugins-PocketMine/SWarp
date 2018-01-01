<?php

namespace solo\swarp;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use solo\swarp\event\WarpCreateEvent;

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

    WarpOptionFactory::init();
  }

  public function onEnable(){
    @mkdir($this->getDataFolder());

    $this->saveResource("setting.yml");
    $this->setting = new Config($this->getDataFolder() . "setting.yml", Config::YAML);

    $this->load();

    $this->titleManager = new TitleManager($this);

    $this->shortcutManager = new ShortcutManager($this);

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

    $this->shortcutManager = null;
    $this->titleManager = null;
    if(self::$instance !== null){
      self::$instance = null;
    }
  }

  public function getSetting() : Config{
    return $this->setting;
  }

  public function getShortcutManager() : ShortcutManager{
    return $this->shortcutManager;
  }

  public function getTitleManager() : TitleManager{
    return $this->titleManager;
  }



  public function addWarp(Warp $warp) : Warp{
    if(isset($this->warps[$name = strtolower($warp->getName())])){
      throw new WarpAlreadyExistsException("\"" . $name . "\" 이름의 워프는 이미 존재합니다");
    }
    $this->getServer()->getPluginManager()->callEvent($ev = new WarpCreateEvent($warp));
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
      throw new WarpNotExistsException("\"" . $warp . "\" 이름의 워프는 존재하지 않습니다.");
    }
    $warpInstance = $this->warps[$warp];
    unset($this->warps[strtolower($warp)]);
    return $warpInstance;
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

  private function load(){
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
