<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CronGetExchangeRateCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cron:get-nbr-rate')
            ->setDescription('Gets the current exchange rate from National Bank of Romania and stores the result as JSON.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $destination = dirname($this->getContainer()->getParameter('kernel.cache_dir'), 2) . '/nbr_exchange_rate.json';
        $request = $this->getContainer()->get('exchange_rate');

        $result = file_put_contents($destination, $request->getJsonResponse());

        if ((bool) $result) {
            $output->writeln("NBR exchange rate data cached in <comment>{$destination}</comment>");
        } else {
            $output->writeln("Failed to cache data!");
        }
    }
}
