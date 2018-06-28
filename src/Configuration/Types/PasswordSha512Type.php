<?php

namespace GdprTools\Configuration\Types;

use Faker\Factory;
use GdprTools\Configuration\TypeInterface;

class PasswordSha512Type implements TypeInterface
{
  /**
   * {@inheritdoc}
   */
  public static function name() {
    return 'password-sha512';
  }

  /**
   * {@inheritdoc}
   */
  public static function anonymise()
  {
    $faker = Factory::create();

    return hash('sha512', $faker->password);
  }
}
