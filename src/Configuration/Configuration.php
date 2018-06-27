<?php

namespace GdprTools\Configuration;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;

class Configuration {

  /** @var string $file */
  protected $file;

  /** @var array $configuration */
  protected $configuration;

  /** @var \Symfony\Component\Console\Style\SymfonyStyle $io */
  protected $io;

  /**
   * Configuration constructor.
   *
   * @param $file
   * @param \Symfony\Component\Console\Style\SymfonyStyle $io
   */
  public function __construct($file, SymfonyStyle $io)
  {
    $this->file = $file;
    $this->io = $io;

    $this->configuration = Yaml::parseFile($file);
  }

  /**
   * Returns the yaml configuration as an array.
   *
   * @return array
   */
  public function toArray() {
    return $this->configuration;
  }

  /**
   * Checks if all provided keys are available in the configuration.
   * Supports nested keys by passing a nested array as parameter.
   *
   * @param array $keys
   * @param bool $exitOnErrors
   * @param array $configuration
   * @param string $nesting
   *
   * @return bool
   */
  public function isAvailable(array $keys, $exitOnErrors = false, array $configuration = null, $nesting = '') {
    if ($configuration === null) {
      $configuration = $this->configuration;
    }

    $isAvailable = true;

    // Check all keys that are an array (and recursively check the array items)
    foreach (array_keys($keys) as $key) {
      if (is_array($keys[$key])) {
        if (!array_key_exists($key, $configuration)) {
          $isAvailable = false;
          $this->io->error($nesting . $key . ' does not exist in configuration.');
        }
        else {
          $nestedAvailable = $this->isAvailable($keys[$key], false, $configuration[$key], $key . ':' . $nesting);

          if (!$nestedAvailable) {
            $isAvailable = false;
          }
        }
      }
    }

    // Check all keys that are not an array
    foreach ($keys as $key) {
      if (!is_array($key) && !array_key_exists($key, $configuration)) {
        $isAvailable = false;
        $this->io->error($nesting . $key . ' does not exist in configuration.');
      }
    }

    if (!$isAvailable && $exitOnErrors) {
      die();
    }

    return $isAvailable;
  }
}
