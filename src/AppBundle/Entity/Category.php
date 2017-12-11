<?php
/**
 * Created by PhpStorm.
 * User: hktr92
 * Date: 7/6/17
 * Time: 6:55 PM
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Category
 * @package AppBundle\Entity
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 * @ORM\Table(name="category")
 */
class Category
{
    /**
     * @var integer
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\Category",
     *     inversedBy="children",
     *     cascade={"persist"},
     *     fetch="EXTRA_LAZY"
     * )
     * @ORM\JoinColumn(
     *     name="parent_id",
     *     referencedColumnName="id",
     *     nullable=true,
     *     onDelete="SET NULL"
     * )
     */
    private $parent;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Gedmo\Slug(fields={"name"})
     */
    private $slug;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\Article",
     *     mappedBy="category",
     *     fetch="EXTRA_LAZY"
     * )
     */
    private $articles;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\Category",
     *     mappedBy="parent",
     *     fetch="EXTRA_LAZY"
     * )
     */
    private $children;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

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
     * @return Category
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
     * Set slug
     * @param string $slug
     * @return Category
     */
    public function setSlug($slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     * @return string
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Set position
     * @param integer $position
     * @return Category
     */
    public function setPosition($position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     * @return integer
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * Set isActive
     * @param boolean $isActive
     * @return Category
     */
    public function setIsActive($isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     * @return bool
     */
    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    /**
     * Set parent
     * @param \AppBundle\Entity\Category $parent
     * @return Category
     */
    public function setParent(Category $parent = null): self
    {
        $this->parent = $parent;
        $parent->addChild($this);

        return $this;
    }

    /**
     * Get parent
     * @return \AppBundle\Entity\Category
     */
    public function getParent(): ?Category
    {
        return $this->parent;
    }

    /**
     * Add article
     * @param \AppBundle\Entity\Article $article
     * @return Category
     */
    public function addArticle(Article $article): self
    {
        if ($this->articles->contains($article)) {
            return $this;
        }

        $this->articles[] = $article;
        $article->setCategory($this);

        return $this;
    }

    /**
     * Remove article
     * @param \AppBundle\Entity\Article $article
     */
    public function removeArticle(Article $article): void
    {
        $this->articles->removeElement($article);
        $article->setCategory(null);
    }

    /**
     * Get articles
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getArticles(): ?Collection
    {
        return $this->articles;
    }

    /**
     * Add child
     * @param \AppBundle\Entity\Category $child
     * @return Category
     */
    public function addChild(Category $child): self
    {
        $this->children->add($child);

        return $this;
    }

    /**
     * Remove child
     * @param \AppBundle\Entity\Category $child
     */
    public function removeChild(Category $child): void
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren(): ?Collection
    {
        return $this->children;
    }
}
