<?php
/**
 * Created by PhpStorm.
 * User: hktr92
 * Date: 7/12/17
 * Time: 5:07 PM
 */

namespace AppBundle\Command;

use AppBundle\Exception\InvalidJsonException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Repository\RepositoryFactory;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class ToolboxAbstractCommand extends ContainerAwareCommand
{
    private $jsonData;

    protected function getDoctrine(): EntityManagerInterface
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    protected function readJsonData(string $file): self
    {
        $jsonDirectory = $this->getContainer()->getParameter('kernel.root_dir') . '/Data';
        $jsonFile = "{$jsonDirectory}/{$file}.json";

        if (!(new Filesystem())->exists($jsonFile)) {
            throw new \InvalidArgumentException("Sorry, no '{$jsonFile}' found in '{$jsonDirectory}'.");
        }

        $this->jsonData = json_decode(file_get_contents($jsonFile), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidJsonException(sprintf('Json file "%s" is invalid: %s', $jsonFile, json_last_error_msg()));
        }

        return $this;
    }

    protected function getJsonData(): array
    {
        return $this->jsonData;
    }

    protected function convertCase($string, $case = MB_CASE_TITLE): string
    {
        return mb_convert_case($string, $case, 'UTF-8');
    }

    protected function encodePassword(UserInterface $user, $plainPassword): string
    {
        return $this->getContainer()->get('security.password_encoder')->encodePassword($user, $plainPassword);
    }
}