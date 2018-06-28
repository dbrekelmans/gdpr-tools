<?php

namespace GdprTools\Configuration\Types;

use Faker\Factory;
use GdprTools\Configuration\TypeInterface;

class StringType implements TypeInterface
{

  /**
   * {@inheritdoc}
   */
  public static function name()
  {
    return 'string';
  }

  /**
   * {@inheritdoc}
   */
  public static function anonymise()
  {
    $faker = Factory::create();

    return $faker->text(255);
  }
}
