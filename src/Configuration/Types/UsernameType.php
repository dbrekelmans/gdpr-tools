<?php

namespace GdprTools\Configuration\Types;

use Faker\Factory;
use GdprTools\Configuration\TypeBase;
use GdprTools\Configuration\TypeInterface;

class UsernameType extends TypeBase implements TypeInterface
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
  public static function anonymise($unique = false, array $options = [])
  {
    $faker = Factory::create();

    if ($unique) {
      $faker = $faker->unique();
    }

    return $faker->userName;
  }
}
