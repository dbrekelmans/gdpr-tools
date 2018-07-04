<?php

namespace GdprTools\Configuration;

interface TypeInterface
{

  /**
   * Returns the type name that can be used in the configuration.
   *
   * @return string
   */
  public static function name();

  /**
   * Returns the available options.
   *
   * @return array
   */
  public static function options();

  /**
   * Returns the default options.
   *
   * @return array
   */
  public static function defaultOptions();

  /**
   * Returns if the value is supported for this option.
   *
   * @param $option
   * @param $value
   *
   * @return boolean
   */
  public static function isSupported($option, $value);

  /**
   * Returns anonymous content in this type's format.
   *
   * @param bool $unique
   * @param array $options
   *
   * @return mixed
   */
  public static function anonymise($unique = false, array $options = []);
}
