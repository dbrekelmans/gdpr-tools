<?php

namespace GdprTools\Configuration\Types;

use Faker\Factory;
use GdprTools\Configuration\TypeBase;
use GdprTools\Configuration\TypeInterface;

class IpAddressType extends TypeBase implements TypeInterface
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
  public static function anonymise($unique = false, array $options = [])
  {
    $faker = Factory::create();

    if ($unique) {
      $faker = $faker->unique();
    }

    return $faker->ipv4;
  }
}
