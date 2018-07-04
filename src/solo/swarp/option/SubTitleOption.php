<?php

namespace solo\swarp\option;

use solo\swarp\SWarp;
use solo\swarp\WarpException;
use solo\swarp\WarpOption;
use solo\swarp\event\PlayerWarpEvent;
use solo\swarp\option\argument\ArgumentString;

class SubTitleOption extends WarpOption{

    /** @var string */
    private $subTitleMessage;

    public function __construct(ArgumentString ...$args){
        $this->subTitleMessage = implode(" ", $args);
    }

    public function getName() : string{
        return "서브타이틀";
    }

    public function apply(PlayerWarpEvent $event){
        SWarp::getInstance()->getTitleManager()->addSubTitle($event->getPlayer(), $this->subTitleMessage);
    }

    public function __toString(){
        return $this->getName() . " : " . $this->subTitleMessage;
    }

    protected function dataSerialize() : array{
        return [
            "subTitleMessage" => $this->subTitleMessage
        ];
    }

    protected function dataDeserialize(array $data) : void{
        $this->subTitleMessage = $data["subTitleMessage"];
    }
}
