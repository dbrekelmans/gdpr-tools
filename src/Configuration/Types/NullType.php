<?php

namespace GdprTools\Configuration\Types;

use GdprTools\Configuration\TypeInterface;

class NullType implements TypeInterface
{

  public static function name() {
    return 'null';
  }
}
