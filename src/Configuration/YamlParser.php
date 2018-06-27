<?php

namespace GdprTools\Configuration;

use Symfony\Component\Yaml\Yaml;

class YamlParser {

  private $configuration;

  public function __construct($file)
  {
    $this->configuration = Yaml::parseFile($file);
  }

  public function getConfiguration() {
    return $this->configuration;
  }
}
