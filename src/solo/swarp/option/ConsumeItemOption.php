<?php

namespace solo\swarp\option;

use solo\swarp\SWarp;
use solo\swarp\WarpException;
use solo\swarp\WarpOption;
use solo\swarp\event\PlayerWarpEvent;
use solo\swarp\option\argument\ArgumentNatural;
use solo\swarp\option\argument\ArgumentString;
use pocketmine\item\Item;

class ConsumeItemOption extends WarpOption{

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
        return "아이템소모";
    }

    public function test(PlayerWarpEvent $event){
        foreach($this->items as $item){
            if(!$event->getPlayer()->getInventory()->contains($item)){
                throw new WarpException("아이템을 가지고 있지 않습니다.");
            }
        }
    }

    public function apply(PlayerWarpEvent $event){
        $event->getPlayer()->getInventory()->removeItem(...$this->items);
        $event->getPlayer()->sendMessage(SWarp::$prefix . "워프하여 " . implode(", ", $this->items) . " 아이템을 소모하였습니다.");
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
