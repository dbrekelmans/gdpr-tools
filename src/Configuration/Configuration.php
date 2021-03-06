<?php

namespace GdprTools\Configuration;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class Configuration {
  const ENV_VAR_PREFIX = '${';
  const ENV_VAR_SUFFIX = '}';

  const TRUNCATE = 'truncate';
  const ANONYMISE = 'anonymise';

  const ANONYMISE_PRESETS = 'presets';
  const ANONYMISE_CUSTOM = 'custom';
  const ANONYMISE_EXCLUDE = 'exclude';

  const ANONYMISE_COLUMN_TYPE = 'type';
  const ANONYMISE_COLUMN_UNIQUE = 'unique';

  const ANONYMISE_TYPE_NAME = 'name';
  const ANONYMISE_TYPE_OPTIONS = 'options';

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
    $this->io = $io;

    try {
      $this->configuration = Yaml::parseFile($file);
    }
    catch(ParseException $e) {
      $this->io->error($file . ' is not a valid yaml configuration.');
      die;
    }

    $this->replaceEnvVars();
    $this->addPresets();
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
   * @param string $preset
   * @param string $table
   *
   * @return array
   */
  public function getExclude($preset, $table) {
    if (!$this->isAvailable([
      self::ANONYMISE => [
        self::ANONYMISE_EXCLUDE => [
          $preset => [
            $table
          ],
        ],
      ],
    ], false)) {
      return [];
    }

    return $this->configuration[self::ANONYMISE][self::ANONYMISE_EXCLUDE][$preset][$table];
  }

  /**
   * Adds all presets to the configuration array.
   */
  protected function addPresets() {
    if (!$this->isAvailable([
      self::ANONYMISE => [
        self:: ANONYMISE_PRESETS,
      ],
    ], false, false)) {
      return;
    }

    $presets = $this->configuration[self::ANONYMISE][self::ANONYMISE_PRESETS];
    foreach($presets as $preset) {
      $this->addPreset($preset);
    }
  }

  /**
   * Adds a preset to the configuration array
   *
   * @param string $preset
   */
  protected function addPreset($preset) {
    try {
      $this->configuration[self::ANONYMISE][$preset] = Yaml::parseFile(__DIR__ . '/Presets/' . $preset . '.yml');
    }
    catch (ParseException $e) {
      $this->io->error($preset . ' is not a valid preset.');
      die;
    }
  }

  /**
   * @param string $key
   */
  protected function printNotAvailableError($key) {
    $this->io->error($key . ' does not exist in configuration.');
  }

  /**
   * Replaces env vars in the configuration with actual env var values.
   */
  protected function replaceEnvVars() {
    array_walk_recursive($this->configuration, function (&$value) {
      if (
        substr($value, 0, strlen(self::ENV_VAR_PREFIX)) === self::ENV_VAR_PREFIX &&
        substr($value, -1, strlen(self::ENV_VAR_SUFFIX)) === self::ENV_VAR_SUFFIX
      ) {
        $tempValue = $value;

        $tempValue = substr($tempValue, strlen(self::ENV_VAR_PREFIX));
        $tempValue = substr($tempValue, 0, strlen($tempValue) - strlen(self::ENV_VAR_SUFFIX));

        $envValue = getenv($tempValue);

        if ($envValue === false) {
          $this->io->error($tempValue . ' is not an existing environment variable.');
          die;
        }

        $value = $envValue;
      }
    });
  }
}
