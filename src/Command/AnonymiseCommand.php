<?php

namespace GdprTools\Command;

use Doctrine\DBAL\Connection;
use GdprTools\Configuration\Configuration;
use GdprTools\Database\Anonymiser;
use GdprTools\Database\Database;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AnonymiseCommand extends Command
{

  const ARGUMENT_FILE = 'file';

  /**
   * {@inheritdoc}
   */
  protected function configure()
  {
    $this
      ->setName('db:anonymise')
      ->setDescription('Anonymises a database based on a yaml configuration.')
      ->setHelp('Anonymises a database based on a yaml configuration.')
      ->addArgument(
        self::ARGUMENT_FILE,
        InputArgument::REQUIRED,
        'Where is the yaml configuration located?')
    ;
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $io = new SymfonyStyle($input, $output);

    $file = $input->getArgument(self::ARGUMENT_FILE);

    $configuration = new Configuration($file, $io);
    $database = new Database($configuration, $io);
    $connection = $database->getConnection();

    $io->success('Database connection succeeded.');

    $this->printTable('anonymise_test', $connection, $io);

    $anonymiser = new Anonymiser();
    $anonymiser->anonymise($configuration, $io);

    $this->printTable('anonymise_test', $connection, $io);
  }


  /*
   * TODO: Remove. For debugging purposes only.
   */
  protected function printTable($table, Connection $connection, SymfonyStyle $io) {
    $result = $connection->query('SELECT * FROM ' . $table);

    $rows = [];
    while ($row = $result->fetch()) {
      array_push($rows, $row);
    }

    $io->table(array_keys($rows[0]), $rows);
  }
}
