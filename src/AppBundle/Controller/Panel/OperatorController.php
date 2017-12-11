<?php
/**
 * Created by PhpStorm.
 * User: hktr92
 * Date: 7/7/17
 * Time: 8:09 PM
 */

namespace AppBundle\Controller\Panel;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class OperatorController
 * @package AppBundle\Controller\Panel
 * @Route("/operator")
 * @Security("is_granted('ROLE_SYSOP')")
 */
class OperatorController extends Controller
{
    /**
     * @Route("/phpinfo", name="panel_operator_phpinfo")
     */
    public function phpInfoController(): Response
    {
        ob_start();
        phpinfo(INFO_GENERAL);
        $phpinfo_general = ob_get_clean();

        ob_start();
        phpinfo(INFO_CONFIGURATION);
        $phpinfo_configuration = ob_get_clean();

        ob_start();
        phpinfo(INFO_MODULES);
        $phpinfo_modules = ob_get_clean();

        ob_start();
        phpinfo(INFO_ENVIRONMENT);
        $phpinfo_environment = ob_get_clean();

        ob_start();
        phpinfo(INFO_VARIABLES);
        $phpinfo_variables = ob_get_clean();

        return $this->render('Panel/operator_phpinfo.html.twig', [
            'info_general' => $phpinfo_general,
            'info_configs' => $phpinfo_configuration,
            'info_modules' => $phpinfo_modules,
            'info_env'     => $phpinfo_environment,
            'info_var'     => $phpinfo_variables,
        ]);
    }

    /**
     * @Route("/profiler", name="panel_operator_profiler")
     */
    public function profilerController(): Response
    {
        return $this->render('Panel/operator_profiler.html.twig', [

        ]);
    }

    /**
     * @Route("/logs", name="panel_operator_logs")
     */
    public function logsController(): Response
    {
        $logsDir = $this->getParameter('kernel.logs_dir');
        $env = $this->getParameter('kernel.environment');
        $filename = "{$logsDir}/{$env}.log";
        $fs = new Filesystem();
        $file = [];

        if (!$fs->exists($filename)) {
            $state = 'inexistent';
        } else {
            if (!is_readable($filename)) {
                $state = 'unreadable';
            } else {
                $file = file($filename);
                $state = 'ok';

                if (empty($file)) {
                    $state = 'empty';
                }
            }
        }

        return $this->render('Panel/operator_logs.html.twig', [
            'file'  => $file,
            'state' => $state,
        ]);
    }

    /**
     * @Route("/logs/clear", name="panel_operator_logs_clear")
     */
    public function logsClearController(): Response
    {
        $logsDir = $this->getParameter('kernel.logs_dir');
        $env = $this->getParameter('kernel.environment');
        $filename = "{$logsDir}/{$env}.log";
        $fs = new Filesystem();

        if ($fs->exists($filename)) {
            $fs->remove($filename);
            $fs->touch($filename);
            $fs->chmod($filename, '0777');
        }

        return $this->redirectToRoute('panel_operator_logs');
    }
}