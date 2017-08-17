<?php

namespace solo\swarp\option;

use solo\swarp\WarpOption;

class ShortcutOption extends WarpOption{

  public function __construct(string $value = ""){

  }

  public function getName() : string{
    return "쇼트컷";
  }

  public function __toString(){
    return $this->getName();
  }
}
