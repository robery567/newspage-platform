<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\ProcessBuilder;

class PlatformUpdateCommand extends ContainerAwareCommand
{
    use LockableTrait;

    private function clear($buffer)
    {
        $eol = PHP_EOL;

        $clean = $buffer;
        $clean = preg_replace("/\033\[[^m]*m/", '', $clean);
        $clean = preg_replace("/({$eol})/", '', $clean);

        return $clean;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('platform:update')
            ->setAliases(['sf:up'])
            ->setDescription('Updates the platform version.')
        ;
    }

    private function registerHooks()
    {
        $root = dirname($this->getContainer()->getParameter('kernel.root_dir'));

        return [
            'pre' => [
                'cache_clear' => [
                    'prefix' => $root . '/bin/console',
                    'arguments' => ['cache:clear', '--no-warmup'],
                    'working_dir' => $root,
                ],
            ],
            'on' => [
                'git_fetch' => [
                    'prefix' => '/usr/bin/git',
                    'arguments' => ['fetch', '--all'],
                    'working_dir' => $root,
                ],
                'git_reset' => [
                    'prefix' => '/usr/bin/git',
                    'arguments' => ['reset', '--hard', 'origin/master'],
                    'working_dir' => $root,
                ],
            ],
            'post' => [
                // TODO improve this one to install if new package detected || update
                'composer_update' => [
                    'prefix' => $root . '/bin/console',
                    'arguments' => ['--no-interaction', 'composer:update'],
                    'working_dir' => $root,
                ],
                'database_update' => [
                    'prefix' => $root . '/bin/console',
                    'arguments' => ['--no-interaction', 'doctrine:migrations:migrate'],
                    'working_dir' => $root,
                ],
                'cache_clear' => [
                    'prefix' => $root . '/bin/console',
                    'arguments' => ['--no-interaction', 'cache:clear --no-warmup'],
                    'working_dir' => $root,
                ],
                'cache_warmup' => [
                    'prefix' => $root . '/bin/console',
                    'arguments' => ['--no-interaction', 'cache:warmup'],
                    'working_dir' => $root,
                ],
            ],
        ];
    }

    private function registerStyles(OutputInterface $output)
    {
        $formatter = $output->getFormatter();
        $formatter->setStyle('hook_header', new OutputFormatterStyle('black', 'cyan'));
        $formatter->setStyle('hook_name', new OutputFormatterStyle('yellow', 'black'));
        $formatter->setStyle('hook_type', new OutputFormatterStyle('yellow'));
        $formatter->setStyle('hook_command', new OutputFormatterStyle('blue'));
        $formatter->setStyle('hook_ok', new OutputFormatterStyle('green'));
        $formatter->setStyle('hook_fail', new OutputFormatterStyle('red'));
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->registerStyles($output);
        $formatter = $this->getHelper('formatter');
        $logger = $this->getContainer()->get('logger');


        if (!$this->lock()) {
            $output->writeln('Error: You cannot run this command more than once. Wait until it is executed.');
            return 0;
        }

        $output->writeln('');
        foreach ($this->registerHooks() as $event => $operations) {
            foreach ($operations as $hook => $commands) {
                $output->writeln($formatter->formatBlock(['Running hook:', $hook], 'hook_header'));
                $processBuilder = new ProcessBuilder();
                $processBuilder->setPrefix($commands['prefix'])
                    ->setArguments($commands['arguments'])
                    ->setWorkingDirectory($commands['working_dir'])
                    ->setTimeout(null);

                $hookName = str_replace('_', ' ', $hook);
                $output->writeln("<hook_type>{$event}Hook</hook_type>> <hook_command>{$hookName}</hook_command>: executing...");

                try {
                    $process = $processBuilder->getProcess();
                    $process->run(function ($type, $buffer) use ($input, $output, $logger, $hookName, $event) {
                        $text = explode(PHP_EOL, $buffer);
                        foreach ($text as $line) {
                            $output->writeln("[{$event}] [<hook_ok> OK </hook_ok>] <hook_command>{$hookName}</hook_command>: " . $this->clear($line));
                        }
                    });

                    $output->writeln("<hook_type>{$event}Hook</hook_type>> hook <hook_command>{$hookName}</hook_command> executed!");
                } catch (ProcessFailedException $e) {
                    $output->writeln("[<hook_fail>FAIL</hook_fail>] <hook_type>{$event}Hook</>> <hook_command>{$hookName}</hook_command>: failed to execute command.");
                } catch (ProcessTimedOutException $e) {
                    $output->write("[<hook_fail>FAIL</hook_fail>] <hook_type>{$event}Hook</>> <hook_command>{$hookName}</hook_command>: command execution timed out.");
                }

                $output->writeln('');
            }
        }

        $this->release();
    }
}
