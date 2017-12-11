<?php
namespace AppBundle\Util;


use Symfony\Component\DependencyInjection\ContainerInterface;

class MaintenanceHandler
{
    private $lockFile = null;

    public function __construct(ContainerInterface $container)
    {
        $lockDir = $container->getParameter('kernel.project_dir') . '/var';

        $this->lockFile = "{$lockDir}/maintenance.lock";
    }

    public function lock()
    {
        return file_put_contents($this->lockFile, (new \DateTime())->format(DATE_ISO8601));
    }

    public function unlock()
    {
        return @unlink($this->lockFile);
    }

    public function isLocked()
    {
        return file_exists($this->lockFile);
    }

    public function lastLocked()
    {
        return ($this->isLocked()) ? file_get_contents($this->lockFile) : null;
    }
}