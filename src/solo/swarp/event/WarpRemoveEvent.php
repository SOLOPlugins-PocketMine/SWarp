<?php

namespace solo\swarp\event;

use pocketmine\event\Cancellable;

class WarpRemoveEvent extends WarpEvent implements Cancellable{

  public static $handlerList = null;

}
