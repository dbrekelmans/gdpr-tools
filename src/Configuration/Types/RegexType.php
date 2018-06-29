<?php

namespace GdprTools\Configuration\Types;

use GdprTools\Configuration\TypeInterface;

class RegexType implements TypeInterface
{
  /**
   * {@inheritdoc}
   */
  public static function name() {
    return 'regex';
  }

  /**
   * {@inheritdoc}
   */
  public static function anonymise()
  {
    // TODO
    return '';
  }
}
