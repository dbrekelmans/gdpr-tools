<?php

namespace GdprTools\Database;

use Doctrine\DBAL\DBALException;
use GdprTools\Configuration\Configuration;
use GdprTools\Configuration\TypeFactory;
use Symfony\Component\Console\Style\SymfonyStyle;

class Anonymiser
{
  const OPERATOR_IS = '=';
  const OPERATOR_IS_NOT = '!=';

  /**
   * Anonymises database based on the configuration.
   *
   * @param \GdprTools\Configuration\Configuration $configuration
   * @param \Symfony\Component\Console\Style\SymfonyStyle $io
   */
  public function anonymise(Configuration $configuration, SymfonyStyle $io) {
    $this->anonymiseCustom($configuration, $io);
    $this->anonymisePreset($configuration, $io);
  }

  /**
   * Anonymises all custom tables found in the configuration
   *
   * @param \GdprTools\Configuration\Configuration $configuration
   * @param \Symfony\Component\Console\Style\SymfonyStyle $io
   */
  protected function anonymiseCustom(Configuration $configuration, SymfonyStyle $io) {
    if (!$configuration->isAvailable([Configuration::CATEGORY_CUSTOM])) {
      return;
    }

    $database = new Database($configuration, $io);
    $connection = $database->getConnection();

    $custom = $configuration->toArray()[Configuration::CATEGORY_CUSTOM];
    if (!is_array($custom)) {
      $io->error(Configuration::CATEGORY_CUSTOM . ' does not contain tables in the configuration.');
      return;
    }

    $tables = array_keys($custom);

    foreach ($tables as $table) {
      $except = $configuration->getExcept(Configuration::CATEGORY_CUSTOM, $table);
      $columns = $custom[$table];

      if (!is_array($columns)) {
        $io->error($table . ' is does not contain columns in the configuration.');
        return;
      }

      try {
        $result = $connection->query('SELECT * FROM ' . $table);
      }
      catch (DBALException $e) {
        $io->error($e);
        return;
      }

      while ($row = $result->fetch()) {

        $headers = array_keys($row);

        $values = $row;
        foreach ($columns as $column => $type) {
          if (!in_array($column, $headers)) {
            $io->error($column . ' does not exist in database.');
            return;
          }

          $typeObject = TypeFactory::instance()->create($type);

          if ($typeObject === null) {
            $io->error($type . ' type does not exist.');
            return;
          }

          $values[$column] = $typeObject->anonymise();
        }

        $set = $this->prepareSet($headers, $values);
        $where = $this->prepareWhere($row, $except);

        try {
          $connection->query('UPDATE ' . $table . ' SET ' . $set . ' WHERE ' . $where . ';');

        }
        catch (DBALException $e) {
          $io->error($e);
          return;
        }
      }

      $io->success('Successfully anonymised ' . $table . '.');
    }
  }

  /**
   * Anonymises all presets found in the configuration.
   *
   * @param \GdprTools\Configuration\Configuration $configuration
   * @param \Symfony\Component\Console\Style\SymfonyStyle $io
   */
  protected function anonymisePreset(Configuration $configuration, SymfonyStyle $io) {
    if (!$configuration->isAvailable(['presets'])) {
      return;
    }
  }

  /**
   * Prepares the SET for a database update query.
   *
   * @param array $headers
   * @param array $values
   *
   * @return string
   */
  protected function prepareSet(array $headers, array $values) {
    $set = [];

    foreach ($headers as $header) {
      $value = $this->prepareValue($values[$header], '=');

      array_push($set, '`' . $header . '` ' . $value . '');
    }

    return implode(', ', $set);
  }

  /**
   * Prepares a where statement for a database query.
   *
   * @param array $row
   * @param array $except
   *
   * @return string
   */
  protected function prepareWhere(array $row, array $except) {
    $headers = array_keys($row);

    $where = [];

    foreach ($headers as $header) {
      $value = $this->prepareValue($row[$header], '=', true);

      array_push($where, '`' . $header . '` ' . $value . '');
    }

    $exceptHeaders = array_keys($except);

    foreach ($exceptHeaders as $exceptHeader) {
      foreach ($except[$exceptHeader] as $value) {
        $value = $this->prepareValue($value, '!=', true);

        array_push($where, '`' . $exceptHeader . '` ' . $value . '');
      }
    }

    return implode(' AND ', $where);
  }

  /**
   * Prepares a value for a database query.
   *
   * @param mixed $value
   *
   * @param $operator
   * @param bool $isWhere
   *
   * @return string
   */
  protected function prepareValue($value, $operator, $isWhere = false) {
    if ($value === null) {
      $value = 'NULL';

      if ($isWhere && $operator === $this::OPERATOR_IS) {
        $operator = 'IS';
      }
      else if ($isWhere && $operator === $this::OPERATOR_IS_NOT) {
        $operator = 'IS NOT';
      }
    }
    else if (!is_int($value)) {
      $value = '\''. $value .'\'';
    }

    return $operator . ' ' . $value;
  }
}
