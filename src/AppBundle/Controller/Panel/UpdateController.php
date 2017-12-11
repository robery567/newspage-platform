<?php
namespace AppBundle\Controller\Panel;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SensioLabs\AnsiConverter\AnsiToHtmlConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class UpdateController
 * @package AppBundle\Controller\Panel
 *
 * @Route("/operator")
 */
class UpdateController extends Controller
{
    /**
     * @Route("/update", name="operator_update")
     */
    public function updateAction(Request $request, KernelInterface $kernel): Response
    {
        $hookCommand = new Application($kernel);
        $hookCommand->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'bistr:update',
        ]);
        $output = new BufferedOutput(OutputInterface::VERBOSITY_NORMAL, true);

        $hookCommand->run($input, $output);

        return $this->render('Platform/platform_update.html.twig', [
            'results' => (new AnsiToHtmlConverter())->convert($output->fetch()),
        ]);
    }
}