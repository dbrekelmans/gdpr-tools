<?php

namespace GdprTools\Configuration\Types;

use Faker\Factory;
use GdprTools\Configuration\TypeInterface;

class EmailType implements TypeInterface
{
  /**
   * {@inheritdoc}
   */
  public static function name() {
    return 'email';
  }

  /**
   * {@inheritdoc}
   */
  public static function anonymise()
  {
    $faker = Factory::create();

    return $faker->email;
  }
}
