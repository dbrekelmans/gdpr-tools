<?php

namespace GdprTools\Configuration;

abstract class TypeBase implements TypeInterface
{

  /**
   * Returns option value if available, otherwise returns default value.
   *
   * @param string $option
   * @param array $options
   *
   * @return mixed
   */
  public static function getOption($option, $options) {
    // TODO: throw exception / print error when isSupported is false.
    if (isset($options[$option]) && static::isSupported($option, $options[$option])) {
      return $options[$option];
    }

    return static::defaultOptions()[$option];
  }

  /**
   * {@inheritdoc}
   */
  public static function options() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultOptions() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public static function isSupported($option, $value) {
    return false;
  }
}
