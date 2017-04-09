<?php
/**
 * Created by PhpStorm.
 * User: denis
 * Date: 28.03.17
 * Time: 15:03
 */

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class FindTournamentsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        // ...
        $this
            ->setName('app:find-tournaments')
            ->setDescription('Save all tournaments')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // ...
        $event = $this->getContainer()->get('app.parser_controller');
        $ev = $event->findTournaments();
        $output->writeln($ev . ' - success');

    }
}
