<?php
namespace AppBundle\Util;

use AppBundle\Repository\SettingsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Zend\Json\Json;
use AppBundle\Entity\Settings as PersistedSettings;

/**
 * Class Settings
 * @package AppBundle\Util
 *
 * This class provides a layer between database stored site configuration and php objects.
 *
 * Config can be accessed via:
 * - object property, if name is valid
 * - array key, if key name is invalid
 */
class Settings
{
    private $_config;

    use ContainerAwareTrait;

    /**
     * Settings constructor.
     * @param ContainerInterface $container In order to access Symfony's services
     *
     * This method gets configuration from Database and stores them into this class
     */
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);

        $results = $this->container
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Settings')
            ->findAllSettings();

        /** @var \AppBundle\Entity\Settings $result */
        foreach ($results as $result) {
            $this->set($result->getName(), $result->getValue());
        }
    }

    /**
     * Validates the Settings' name. It validates against PHP variable name standard, but an additional dot is allowed.
     *
     * @param string $name The configuration's name
     * @return bool
     */
    private function validate(string $name): bool
    {
        $test = '/[a-zA-Z0-9_\.]/i';

        return (bool)preg_match($test, $name);
    }

    /**
     * Sets Settings from Database Storage into Settings object
     *
     * @param string $name A valid key name: [a-zA-Z0-9_\.]
     * @param mixed $value Configuration values, which are mixed
     * @return Settings
     */
    public function set(string $name, $value, bool $persist = false): self
    {
        if (!$this->validate($name)) {
            throw new \InvalidArgumentException('Config name is invalid!');
        }

        $this->_config[$name] = $value;

        if ($persist) {
            /** @var EntityManagerInterface $em */
            $em = $this->container->get('doctrine.orm.entity_manager');
            $entity = new PersistedSettings();
            $entity->setName($name);
            $entity->setValue(Json::encode(Json::decode($value, Json::TYPE_ARRAY)));

            $em->persist($entity);
            $em->flush();
        }

        return $this;
    }

    /**
     * Gets a setting value from (cached) database result
     *
     * @param string $name  A valid key name
     * @param bool $raw     If true, the value won't be decoded from JSON format
     * @return mixed|null
     */
    public function get(string $name, bool $raw = false)
    {
        if (!$this->exists($name)) {
            return null;
        }

        $return = $this->_config[$name];

        if (false === $raw) {
            $return = Json::decode($return);
        }

        return $return;
    }

    /**
     * Gets a raw setting value from (cached) database result
     *
     * @param string $name  A valid key name
     * @return mixed|null
     */
    public function getRaw(string $name)
    {
        return $this->get($name, true);
    }

    /**
     * Checks if a setting value is available into local results
     *
     * @param string $name
     * @return bool
     */
    public function exists(string $name): bool
    {
        return isset($this->_config[$name]);
    }

    /**
     * Removes a setting from local or persisted storage
     *
     * @param string $name
     * @param bool $persist If passed, the removal will be persisted into the database
     * @return $this
     */
    public function remove(string $name, bool $persist = false)
    {
        if ($this->exists($name)) {
            unset($this->_config[$name]);
        }

        if ($persist) {
            /** @var EntityManagerInterface $em */
            $em = $this->container->get('doctrine.orm.entity_manager');
            /** @var SettingsRepository $repo */
            $repo = $em->getRepository(PersistedSettings::class);
            /** @var PersistedSettings $entity */
            $entity = $repo->findOneBy(['name' => $name]);

            $em->remove($entity);
            $em->flush();
        }

        return $this;
    }
}