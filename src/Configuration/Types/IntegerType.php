<?php

namespace GdprTools\Configuration\Types;

use Faker\Factory;
use GdprTools\Configuration\TypeBase;
use GdprTools\Configuration\TypeInterface;

class IntegerType extends TypeBase implements TypeInterface {

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

    return $faker->numberBetween(self::MIN_VALUE, self::MAX_VALUE);
  }
}
