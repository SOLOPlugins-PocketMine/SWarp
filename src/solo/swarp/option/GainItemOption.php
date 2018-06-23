<?php

namespace solo\swarp\option;

use solo\swarp\SWarp;
use solo\swarp\WarpException;
use solo\swarp\WarpOption;
use solo\swarp\event\PlayerWarpEvent;
use solo\swarp\option\argument\ArgumentNatural;
use solo\swarp\option\argument\ArgumentString;
use pocketmine\item\Item;

class GainItemOption extends WarpOption{

  /** @var Item[] */
  private $items;

  public function __construct(ArgumentString $item, ArgumentNatural $count = null, ArgumentString $nbt = null){
    $itemInstance = Item::fromString($item->getValue());
    if($itemInstance->isNull()){
      throw new \InvalidArgumentException("\"" . $item->getValue() . "\" 아이템을 찾을 수 없습니다.");
    }
    if($count !== null){
      $itemInstance->setCount($count->getValue());
    }
    if($nbt !== null){
      $itemInstance->setCompoundTag($nbt->getValue());
    }
    $this->items = [$itemInstance];
  }

  public function getName() : string{
    return "아이템획득";
  }

  public function test(PlayerWarpEvent $event){
    if(!$event->getPlayer()->getInventory()->canAddItem(Item::get(Item::DIAMOND_SWORD, 0, count($this->items)))){ // test with fake item
      throw new WarpException("인벤토리에 공간이 부족합니다.");
    }
  }

  public function apply(PlayerWarpEvent $event){
    $event->getPlayer()->getInventory()->addItem(...$this->items);
    $event->getPlayer()->sendMessage(SWarp::$prefix . "워프하여 " . implode(", ", $this->items) . " 아이템을 획득하였습니다.");
  }

  public function __toString(){
    return $this->getName() . " : " . implode(", ", $this->items);
  }

  protected function dataSerialize() : array{
    return [
      "items" => array_map(function($item){ return $item->jsonSerialize(); }, $this->items)
    ];
  }

  protected function dataDeserialize(array $data) : void{
    $this->items = array_map(function($itemSerialized){
      return Item::jsonDeserialize($itemSerialized);
    }, $data["items"]);
  }
}
