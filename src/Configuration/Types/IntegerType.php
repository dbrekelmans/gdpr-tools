<?php

namespace GdprTools\Configuration\Types;

use GdprTools\Configuration\TypeInterface;

class IntegerType implements TypeInterface {

  /**
   * {@inheritdoc}
   */
  public static function name() {
    return 'int';
  }

}
