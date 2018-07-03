<?php

namespace GdprTools\Database;

use Doctrine\DBAL\DBALException;
use GdprTools\Configuration\Configuration;
use Symfony\Component\Console\Style\SymfonyStyle;

class Truncator
{
  public function truncate(Configuration $configuration, SymfonyStyle $io) {
    if (!$configuration->isAvailable([Configuration::TRUNCATE], true, true)) {
      $io->error(Configuration::TRUNCATE . ' does not contain tables in the configuration.');
    }

    $tables = $configuration->toArray()[Configuration::TRUNCATE];

    if (!is_array($tables)) {
      return;
    }

    $database = new Database($configuration, $io);
    $connection = $database->getConnection();

    foreach ($tables as $table) {
      try {
        $connection->query('TRUNCATE TABLE ' . $table);
        $io->success($table . ' truncated.');
      }
      catch (DBALException $e) {
        $io->error($e);
      }
    }
  }
}
