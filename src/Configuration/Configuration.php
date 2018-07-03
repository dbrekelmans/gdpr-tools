<?php

namespace GdprTools\Configuration;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;

class Configuration {

  const PRESETS = 'presets';
  const CUSTOM = 'custom';
  const EXCLUDE = 'exclude';

  const COLUMN_TYPE = 'type';
  const COLUMN_UNIQUE = 'unique';

  const TYPE_OPTIONS = 'options';

  /** @var string $file */
  protected $file;

  /** @var array $configuration */
  protected $configuration;

  /** @var \Symfony\Component\Console\Style\SymfonyStyle $io */
  protected $io;

  /**
   * Configuration constructor.
   *
   * @param string $file
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
   * @param bool $printErrors
   * @param bool $dieOnErrors
   * @param array $configuration
   * @param string $nesting
   *
   * @return bool
   */
  public function isAvailable(array $keys, $printErrors = true, $dieOnErrors = false, array $configuration = null, $nesting = '') {
    if ($configuration === null) {
      $configuration = $this->configuration;
    }

    $isAvailable = true;

    // Check all keys that are not an array
    foreach ($keys as $key) {
      if (!is_array($key) && !array_key_exists($key, $configuration)) {
        $isAvailable = false;

        if ($printErrors) {
          $this->printNotAvailableError($nesting . $key);
        }
      }
    }

    // Check all keys that are an array (and recursively check the array items)
    foreach (array_keys($keys) as $key) {
      if (is_array($keys[$key])) {
        if (!array_key_exists($key, $configuration)) {
          $isAvailable = false;

          if ($printErrors) {
            $this->printNotAvailableError($nesting . $key);
          }
        }
        else {
          $nestedAvailable = $this->isAvailable($keys[$key], $printErrors, false, $configuration[$key], $key . ':' . $nesting);

          if (!$nestedAvailable) {
            $isAvailable = false;
          }
        }
      }
    }

    if (!$isAvailable && $dieOnErrors) {
      die();
    }

    return $isAvailable;
  }

  /**
   * @param string $presetOrCustom
   * @param string $table
   *
   * @return array
   */
  public function getExclude($presetOrCustom, $table) {
    if (!$this->isAvailable([
      $this::EXCLUDE => [
        $presetOrCustom => [
          $table
        ]
      ]
    ], false)) {
      return [];
    }

    return $this->configuration[$this::EXCLUDE][$presetOrCustom][$table];
  }

  /**
   * @param string $preset
   *
   * @return array
   */
  public static function fromPreset($preset) {
    return Yaml::parseFile(__DIR__ . '/Presets/' . $preset . '.yml');
  }

  /**
   * @param string $key
   */
  protected function printNotAvailableError($key) {
    $this->io->error($key . ' does not exist in configuration.');
  }
}
