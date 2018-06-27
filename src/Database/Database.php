<?php

namespace GdprTools\Database;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use GdprTools\Configuration\Configuration;
use Symfony\Component\Console\Style\SymfonyStyle;

class Database {

  /** @var \Symfony\Component\Console\Style\SymfonyStyle $io */
  protected $io;

  /** @var \Doctrine\DBAL\Connection $connection */
  protected $connection;

  /**
   * Connection constructor.
   *
   * @param \GdprTools\Configuration\Configuration $configuration
   * @param \Symfony\Component\Console\Style\SymfonyStyle $io
   *
   */
  public function __construct(Configuration $configuration, SymfonyStyle $io) {
    $this->io = $io;

    $configuration->isAvailable([
      'database' => [
        'scheme',
        'host',
        'port',
        'name',
        'user',
        'password',
      ]
    ], true);

    $databaseConfiguration = new \Doctrine\DBAL\Configuration();

    $database = $configuration->toArray()['database'];

    $connectionParams = array(
      'dbname' => $database['name'],
      'user' => $database['user'],
      'password' => $database['password'],
      'host' => $database['host'],
      'port' => $database['port'],
      'driver' => $database['scheme'],
    );

    try {
      $connection = DriverManager::getConnection($connectionParams, $databaseConfiguration);
    }
    catch (DBALException $e) {
      $this->io->error($e);
      die();
    }

    $this->connection = $connection;
  }

  public function getConnection() {
    return $this->connection;
  }
}


