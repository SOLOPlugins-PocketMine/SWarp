<?php

namespace solo\swarp;

use pocketmine\event\Listener;

use solo\swarp\command\ShortcutCommand;
use solo\swarp\event\WarpCreateEvent;
use solo\swarp\event\WarpRemoveEvent;

class ShortcutManager implements Listener{

    /** @var SWarp */
    private $owner;

    public function __construct(SWarp $owner){
        $this->owner = $owner;

        if($this->owner->getSetting()->get("use-warp-shortcut", true) === true){
            foreach($this->owner->getAllWarp() as $warp){
                $this->registerShortcut($warp);
            }
            $this->owner->getServer()->getPluginManager()->registerEvents($this, $this->owner);
        }
    }

    public function registerShortcut(Warp $warp){
        $this->owner->getServer()->getCommandMap()->register("swarp", new ShortcutCommand($this->owner, $warp));

        foreach($this->owner->getServer()->getOnlinePlayers() as $player){
            $player->sendCommandData();
        }
    }

    public function unregisterShortcut(Warp $warp){
        $command = $this->owner->getServer()->getCommandMap()->getCommand($warp->getName());
        if($command instanceof ShortcutCommand){
            $this->owner->getServer()->getCommandMap()->unregister($command);
        }

        foreach($this->owner->getServer()->getOnlinePlayers() as $player){
            $player->sendCommandData();
        }
    }

    /**
     * @priority MONITOR
     *
     * @ignoreCancelled true
     */
    public function handleWarpCreate(WarpCreateEvent $event){
        $this->registerShortcut($event->getWarp());
    }

    /**
     * @priority MONITOR
     *
     * @ignoreCancelled true
     */
    public function handleWarpRemove(WarpRemoveEvent $event){
        $this->unregisterShortcut($event->getWarp());
    }
}
