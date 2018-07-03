<?php

namespace GdprTools\Configuration\Types;

use Faker\Factory;
use GdprTools\Configuration\TypeInterface;

class IntegerType implements TypeInterface {

  const MIN_VALUE = -2147483648;
  const MAX_VALUE = 2147483648;

  /**
   * {@inheritdoc}
   */
  public static function name() {
    return 'int';
  }

  /**
   * {@inheritdoc}
   */
  public static function anonymise($unique = false, array $options = [])
  {
    $faker = Factory::create();

    if ($unique) {
      $faker = $faker->unique();
    }

    return $faker->numberBetween(static::MIN_VALUE, static::MAX_VALUE);
  }
}
