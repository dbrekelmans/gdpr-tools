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
    $presets = $configuration->toArray()[Configuration::ANONYMISE][Configuration::ANONYMISE_PRESETS];
    array_push($presets, Configuration::ANONYMISE_CUSTOM);

    foreach ($presets as $preset) {
      if ($preset !== Configuration::ANONYMISE_CUSTOM) {
        $configuration->isAvailable([
          Configuration::ANONYMISE => [
            $preset
          ]
        ], true, true);
      }
      else {
        if (!$configuration->isAvailable([
          Configuration::ANONYMISE => [
            $preset
          ]
        ], false)) {
          continue;
        }
      }

      $configurationArray = $configuration->toArray()[Configuration::ANONYMISE][$preset];
      if (!is_array($configurationArray)) {
        $io->error($preset . ' does not contain tables in the configuration.');
        return;
      }

      $database = new Database($configuration, $io);
      $connection = $database->getConnection();

      $tables = array_keys($configurationArray);

      foreach ($tables as $table) {
        $exclude = $configuration->getExclude($preset, $table);
        $columns = $configurationArray[$table];

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
          foreach ($columns as $column => $data) {
            if (!in_array($column, $headers)) {
              $io->error($column . ' does not exist in the database.');
              return;
            }

            $configuration->isAvailable([
              $table => [
                $column => [
                  Configuration::ANONYMISE_COLUMN_TYPE,
                ],
              ],
            ], true, true, $configurationArray);

            $type = $configurationArray[$table][$column][Configuration::ANONYMISE_COLUMN_TYPE];
            $unique = false;

            if ($configuration->isAvailable([
                Configuration::ANONYMISE_CUSTOM => [
                  $table => [
                    $column => [
                      Configuration::ANONYMISE_COLUMN_UNIQUE,
                    ],
                  ],
                ],
              ], false) && $configurationArray[$table][$column][Configuration::ANONYMISE_COLUMN_UNIQUE] === true) {
              $unique = true;
            }

            $typeObject = TypeFactory::instance()->create($type);

            if ($typeObject === null) {
              $io->error($type . ' type does not exist.');
              return;
            }

            $values[$column] = $typeObject::anonymise($unique);
          }

          $set = $this->prepareSet($headers, $values);
          $where = $this->prepareWhere($row, $exclude);

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
   * @param array $exclude
   *
   * @return string
   */
  protected function prepareWhere(array $row, array $exclude) {
    $headers = array_keys($row);

    $where = [];

    foreach ($headers as $header) {
      $value = $this->prepareValue($row[$header], '=', true);

      array_push($where, '`' . $header . '` ' . $value . '');
    }

    $excludeHeaders = array_keys($exclude);

    foreach ($excludeHeaders as $excludeHeader) {
      foreach ($exclude[$excludeHeader] as $value) {
        $value = $this->prepareValue($value, '!=', true);

        array_push($where, '`' . $excludeHeader . '` ' . $value . '');
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
