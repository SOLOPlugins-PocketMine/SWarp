<?php

namespace solo\swarp\option;

use pocketmine\block\Block;
use pocketmine\level\Position;
use pocketmine\level\particle\DestroyBlockParticle;
use pocketmine\math\Vector3;

use solo\swarp\WarpException;
use solo\swarp\WarpOption;
use solo\swarp\event\PlayerWarpEvent;

class DestinationParticleOption extends WarpOption{

  public function __construct(){

  }

  public function getName() : string{
    return "도착지점파티클";
  }

  public function apply(PlayerWarpEvent $event){
    $dest = $event->getDestination();

    $destBlock = $dest->getLevel()->getBlock(new Vector3($dest->getFloorX(), $dest->getFloorY() - 1, $dest->getFloorZ()));

    if($destBlock->getId() === Block::AIR){
      $destBlock = Block::get(Block::DIAMOND_BLOCK);
    }

    $i = 3;
    while($i-- > 0){
      $dest->getLevel()->addParticle(new DestroyBlockParticle($dest, $destBlock));
    }
  }

  public function __toString(){
    return $this->getName();
  }
}
