<?php

namespace GdprTools\Configuration\Types;

use Faker\Factory;
use GdprTools\Configuration\TypeInterface;

class IpAddressType implements TypeInterface
{

  /**
   * {@inheritdoc}
   */
  public static function name()
  {
    return 'ip';
  }

  /**
   * {@inheritdoc}
   */
  public static function anonymise()
  {
    $faker = Factory::create();

    return $faker->ipv4;
  }
}
