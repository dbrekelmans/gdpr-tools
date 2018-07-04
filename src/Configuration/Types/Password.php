<?php

namespace GdprTools\Configuration\Types;

use Faker\Factory;
use GdprTools\Configuration\TypeBase;
use GdprTools\Configuration\TypeInterface;

class Password extends TypeBase implements TypeInterface
{
  const OPTION_ENCRYPTION = 'encryption';

  /**
   * {@inheritdoc}
   */
  public static function name() {
    return 'password';
  }

  /**
   * {@inheritdoc}
   */
  public static function options() {
    return [
      self::OPTION_ENCRYPTION,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultOptions() {
    return [
      self::OPTION_ENCRYPTION => 'sha512',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function isSupported($option, $value) {
    if (in_array($value, hash_algos())) {
      return true;
    }

    return false;
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Exception
   */
  public static function anonymise($unique = false, array $options = [])
  {
    $encryption = self::getOption(self::OPTION_ENCRYPTION, $options);

    if (!in_array($encryption, hash_algos())) {
      throw new \Exception($encryption . ' is not a supported encryption method.');
    }

    $faker = Factory::create();

    if ($unique) {
      $faker = $faker->unique();
    }

    return hash($encryption, $faker->password);
  }
}
