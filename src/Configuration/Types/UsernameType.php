<?php

namespace GdprTools\Configuration\Types;

use Faker\Factory;
use GdprTools\Configuration\TypeInterface;

class UsernameType implements TypeInterface
{
  /**
   * {@inheritdoc}
   */
  public static function name()
  {
    return 'username';
  }

  /**
   * {@inheritdoc}
   */
  public static function anonymise()
  {
    $faker = Factory::create();

    return $faker->userName;
  }
}
