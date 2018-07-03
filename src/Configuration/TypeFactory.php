<?php

namespace GdprTools\Configuration;

class TypeFactory
{
  const TYPES_NAMESPACE = 'GdprTools\Configuration\Types';
  const TYPES_DIRECTORY = __DIR__ . '/Types/';

  /** @var \GdprTools\Configuration\TypeFactory|null $instance */
  protected static $instance = null;

  /** @var \GdprTools\Configuration\TypeInterface[] $typeClasses */
  protected $typeClasses;

  /**
   * Singleton.
   * Private constructor to prevent accidental instantiation.
   *
   * @param \GdprTools\Configuration\TypeInterface[] $typeClasses
   */
  private function __construct($typeClasses) {
    $this->typeClasses = $typeClasses;
  }

  /**
   * Returns an instance of this class.
   *
   * @return \GdprTools\Configuration\TypeFactory
   */
  public static function instance() {
    if (!isset(self::$instance)) {
      self::$instance = new TypeFactory(static::getTypeClasses());
    }

    return self::$instance;
  }

  /**
   * @param string $typesString
   *
   * @return \GdprTools\Configuration\TypeInterface|null
   */
  public function create($typesString) {
    $types = explode('|', $typesString);
    $type = $types[array_rand($types)];

    foreach ($this->typeClasses as $typeClass) {
      if ($typeClass::name() === $type) {
        return new $typeClass();
      }
    }

    return null;
  }

  /**
   * Returns all type classes in the Types directory.
   * See: https://stackoverflow.com/questions/22761554/php-get-all-class-names-inside-a-particular-namespace
   *
   * @return \GdprTools\Configuration\TypeInterface[]
   */
  protected static function getTypeClasses() {
    $namespace = self::TYPES_NAMESPACE;

    $files = scandir(self::TYPES_DIRECTORY);

    $classes = array_map(function($file) use ($namespace){
      return $namespace . '\\' . str_replace('.php', '', $file);
    }, $files);

    return array_filter($classes, function($possibleClass){
      return class_exists($possibleClass);
    });
  }
}
