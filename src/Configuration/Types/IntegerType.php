<?php

namespace GdprTools\Configuration\Types;

use GdprTools\Configuration\TypeInterface;

class IntegerType implements TypeInterface {

  public static function name() {
    return 'int';
  }

}
