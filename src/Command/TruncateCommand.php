<?php

namespace GdprTools\Command;

use GdprTools\Configuration\Configuration;
use GdprTools\Database\Anonymiser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TruncateCommand extends Command
{

  const ARGUMENT_FILE = 'file';

  /**
   * {@inheritdoc}
   */
  protected function configure()
  {
    $this
      ->setName('db:truncate')
      ->setDescription('Truncates database tables based on a yaml configuration.')
      ->setHelp('Truncates database tables based on a yaml configuration.')
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

    $truncator = new Truncator();
    $truncator->truncate($configuration, $io);
  }
}
