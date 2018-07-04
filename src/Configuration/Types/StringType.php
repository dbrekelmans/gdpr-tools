<?php

namespace GdprTools\Configuration\Types;

use GdprTools\Configuration\TypeBase;
use GdprTools\Configuration\TypeInterface;

class StringType extends TypeBase implements TypeInterface
{
  const OPTION_MINLENGTH = 'minlength';
  const OPTION_MAXLENGTH = 'maxlength';

  protected static $values = [];

  /**
   * {@inheritdoc}
   */
  public static function name() {
    return 'string';
  }

  /**
   * {@inheritdoc}
   */
  public static function options() {
    return [
      self::OPTION_MINLENGTH,
      self::OPTION_MAXLENGTH,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultOptions() {
    return [
      self::OPTION_MINLENGTH => 1,
      self::OPTION_MAXLENGTH => 255,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function isSupported($option, $value) {
    if (is_int($value)) {
      return true;
    }

    return false;
  }

  /**
   * {@inheritdoc}
   */
  public static function anonymise($unique = false, array $options = []) {
    $minLength = self::getOption(self::OPTION_MINLENGTH, $options);
    $maxLength = self::getOption(self::OPTION_MAXLENGTH, $options);

    $length = mt_rand($minLength, $maxLength);
    $characters = 'abcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
    }

    if (self::isUnique($randomString)) {
      array_push(self::$values, $randomString);
      return $randomString;
    }
    else {
      // TODO: break potential endless loop for small string lengths
      return self::anonymise($unique, $options);
    }
  }

  protected static function isUnique($value) {
    if (in_array($value, self::$values)) {
      return false;
    }

    return true;
  }
}
