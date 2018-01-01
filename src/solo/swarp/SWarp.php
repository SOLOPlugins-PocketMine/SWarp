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

  /** @var WarpManager */
  private $warpManager;

  /** @var TitleManager */
  private $titleManager;

  /** @var ShortcutManager */
  private $shortcutManager;

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

    $this->warpManager = new WarpManager($this);
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
    if($this->warpManager !== null){
      $this->warpManager->save();
      $this->warpManager = null;
    }
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

  public function getWarpManager() : WarpManager{
    return $this->warpManager;
  }

  public function addWarp(Warp $warp) : Warp{
    return $this->warpManager->addWarp($warp);
  }

  public function getWarp(string $name) : ?Warp{
    return $this->warpManager->getWarp($name);
  }

  public function getAllWarp() : array{
    return $this->warpManager->getAllWarp();
  }

  public function removeWarp($warp) : Warp{
    return $this->warpManager->removeWarp($warp);
  }
}
