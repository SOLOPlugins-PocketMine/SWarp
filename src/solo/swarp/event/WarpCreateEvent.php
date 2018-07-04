<?php

namespace solo\swarp\event;

use pocketmine\event\Cancellable;

class WarpCreateEvent extends WarpEvent implements Cancellable{

    public static $handlerList = null;

}
