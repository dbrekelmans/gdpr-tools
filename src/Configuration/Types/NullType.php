<?php

namespace GdprTools\Configuration\Types;

use GdprTools\Configuration\TypeBase;
use GdprTools\Configuration\TypeInterface;

class NullType extends TypeBase implements TypeInterface
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
