<?php

namespace GdprTools\Database;

use GdprTools\Configuration\Configuration;
use Symfony\Component\Console\Style\SymfonyStyle;

class Truncator
{
  public function truncate(Configuration $configuration, SymfonyStyle $io) {
    $database = new Database($configuration, $io);
    $connection = $database->getConnection();

  }
}
