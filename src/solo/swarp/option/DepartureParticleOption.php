<?php

namespace solo\swarp\option;

use pocketmine\block\Block;
use pocketmine\level\Position;
use pocketmine\level\particle\DestroyBlockParticle;
use pocketmine\math\Vector3;

use solo\swarp\WarpEvent;
use solo\swarp\WarpException;
use solo\swarp\WarpOption;

class DepartureParticleOption extends WarpOption{

  public function __construct(string $value = ""){

  }

  public function getName() : string{
    return "출발지점파티클";
  }

  public function apply(WarpEvent $event){
    $departure = $event->getPlayer();

    $departureBlock = $departure->getLevel()->getBlock(new Vector3($departure->getFloorX(), $departure->getFloorY() - 1, $departure->getFloorZ()));

    if($departureBlock->getId() === Block::AIR){
      $departureBlock = Block::get(Block::DIAMOND_BLOCK);
    }

    $i = 3;
    while($i-- > 0){
      $departure->getLevel()->addParticle(new DestroyBlockParticle($departure, $departureBlock));
    }
  }

  public function __toString(){
    return $this->getName();
  }
}
