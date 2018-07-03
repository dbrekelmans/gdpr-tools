<?php

namespace GdprTools\Configuration\Types;

use GdprTools\Configuration\TypeInterface;

class NullType implements TypeInterface
{

  /**
   * {@inheritdoc}
   */
  public static function name() {
    return 'null';
  }

  /**
   * {@inheritdoc}
   */
  public static function anonymise($unique = false, array $options = []) {
    return null;
  }
}
