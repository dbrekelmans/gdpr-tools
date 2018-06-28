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
}
