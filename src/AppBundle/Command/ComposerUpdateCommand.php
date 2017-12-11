<?php

namespace AppBundle\Command;

use Composer\Console\Application as Composer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ComposerUpdateCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('composer:update')
            ->setDescription('Hack to execute composer update command in CLI and WEB environment.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
//        $root = dirname($this->getContainer()->getParameter('kernel.root_dir'));
//
//        $actualComposerHash = hash('md5', file_get_contents($root . '/composer.json'));
//        $cachedComposerHash = json_decode(file_get_contents($root . '/composer.lock'), true)['content-hash'];
//
//        if ($actualComposerHash === $cachedComposerHash) {
//            $output->writeln('<info>Skipped Composer update</info>');
//        } else {
//            $output->writeln('<fg=cyan>Updating Composer packages...</>');

            $home = '/home/' . posix_getpwnam(get_current_user())['name'];
            putenv('HOME='.$home);
            $_ENV['HOME'] = $home;

            $composer = new Composer();
            $composer->setAutoExit(false);
            $composer->run(new ArrayInput(['command' => 'install']), $output);
//        }
    }
}
