<?php
/**
 * Created by PhpStorm.
 * User: hktr92
 * Date: 7/6/17
 * Time: 7:13 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Settings
 * @package AppBundle\Entity
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SettingsRepository")
 * @ORM\Table(name="settings")
 */
class Settings
{
    /**
     * @var integer
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $value;

    /**
     * Get id
     * @return integer
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set name
     * @param string $name
     * @return Settings
     */
    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set value
     * @param string $value
     * @return Settings
     */
    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     * @return string
     */
    public function getValue(): ?string
    {
        return $this->value;
    }
}
