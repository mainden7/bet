<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class SoccerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        // ...
        $this
            ->setName('app:soccer')
            ->setDescription('Find soccer sure bets')
            ->addArgument('number', InputArgument::OPTIONAL, '123')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // ...
        $event = $this->getContainer()->get('app.soccer_controller');
        $ev = $event->findOdds($input->getArgument('number'));
        $output->writeln($ev . ' - success');

    }
}
