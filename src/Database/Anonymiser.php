<?php

namespace GdprTools\Database;

use Doctrine\DBAL\DBALException;
use GdprTools\Configuration\Configuration;
use GdprTools\Configuration\TypeFactory;
use Symfony\Component\Console\Style\SymfonyStyle;

class Anonymiser
{

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
    if (!$configuration->isAvailable(['custom'])) {
      return;
    }

    $database = new Database($configuration, $io);
    $connection = $database->getConnection();

    $custom = $configuration->toArray()['custom'];
    if (!is_array($custom)) {
      $io->error('custom does not contain tables in the configuration.');
      return;
    }

    $tables = array_keys($custom);

    foreach ($tables as $table) {
      $columns = $custom[$table];

      if (!is_array($columns)) {
        $io->error($table . ' is does not contain columns in the configuration.');
        return;
      }

      // TODO: foreach row (get rows by SELECT * FROM db) do foreach columns

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
        $where = $this->prepareWhere($row);

        try {
          echo('UPDATE ' . $table . ' SET ' . $set . ' WHERE ' . $where . ';');
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

  protected function prepareSet(array $headers, array $values) {
    $set = [];

    foreach ($headers as $header) {
      $value = $this->prepareValue($values[$header]);

      array_push($set, '`' . $header . '` = ' . $value . '');
    }

    return implode(', ', $set);
  }

  protected function prepareWhere(array $row) {
    $headers = array_keys($row);

    $where = [];

    foreach ($headers as $header) {
      if ($row[$header] === null) {
        continue;
      }

      $value = $this->prepareValue($row[$header]);

      array_push($where, '`' . $header . '` = ' . $value . '');
    }

    return implode(' AND ', $where);
  }

  protected function prepareValue($value) {
    if ($value === null) {
      $value = 'NULL';
    }
    else if (!is_int($value)) {
      $value = '\''. $value .'\'';
    }

    return $value;
  }
}
