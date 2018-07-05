<?php

namespace GdprTools\Configuration\Types;

use Gajus\Paggern\Exception\RuntimeException;
use Gajus\Paggern\Generator;
use GdprTools\Configuration\TypeBase;
use GdprTools\Configuration\TypeInterface;

class RegexType extends TypeBase implements TypeInterface
{
  const OPTION_PATTERN = 'pattern';

  protected static $values = [];

  /**
   * {@inheritdoc}
   */
  public static function name() {
    return 'regex';
  }

  /**
   * {@inheritdoc}
   */
  public static function options() {
    return [
      self::OPTION_PATTERN,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultOptions() {
    return [
      self::OPTION_PATTERN => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function isSupported($option, $value) {
    // TODO: check if valid pattern
    if (is_string($value)) {
      return true;
    }

    return false;
  }

  /**
   * {@inheritdoc}
   */
  public static function anonymise($unique = false, array $options = [])
  {
    $pattern = self::getOption(self::OPTION_PATTERN, $options);

    $generator = new Generator();

    try {
      $value = $generator->generateFromPattern($pattern);

      if (!self::isUnique($value)) {
        self::anonymise($unique, $options);
      }
      else {
        return $value;
      }
    }
    catch (RuntimeException $e) {
      return null;
    }
  }

  protected static function isUnique($value) {
    if (in_array($value, self::$values)) {
      return false;
    }

    return true;
  }
}
