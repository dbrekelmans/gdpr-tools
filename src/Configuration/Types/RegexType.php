<?php

namespace GdprTools\Configuration\Types;

use GdprTools\Configuration\TypeBase;
use GdprTools\Configuration\TypeInterface;

class RegexType extends TypeBase implements TypeInterface
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
  public static function anonymise($unique = false, array $options = [])
  {
    // TODO
    return '';
  }
}
