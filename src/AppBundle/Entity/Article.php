<?php
/**
 * Created by PhpStorm.
 * User: hktr92
 * Date: 7/6/17
 * Time: 6:33 PM
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Article
 * @package AppBundle\Entity
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ArticleRepository")
 * @ORM\Table(name="article")
 */
class Article
{
    const MAX_PAGE_ARTICLE = 10;
    const MAX_MOST_VIEWED_ARTICLE = 3;
    const MAX_RECOMMENDED_ARTICLE = 6;
    const MAX_RECENT_ARTICLES = 15;
    const MAX_RSS_FEED_ARTICLE = 30;

    const TYPE_NORM = 0;
    const TYPE_VIDEO = 1;
    const TYPE_HOT = 2;
    const TYPE_REC = 3;
    const TYPE_AD = 4;
    const TYPE_FEAT = 5;

    CONST MAX_NORM_ARTICLE = 10;
    CONST MAX_VIDEO_ARTICLE = 5;
    CONST MAX_HOT_ARTICLE = 1;
    CONST MAX_REC_ARTICLE = 6;
    CONST MAX_AD_ARTICLE = 3;

    /**
     * @var integer
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="uuid")
     * @Assert\Uuid()
     */
    private $articleId;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Gedmo\Slug(fields={"title"})
     */
    private $slug;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @Assert\DateTime()
     */
    private $addedAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @Assert\DateTime()
     */
    private $updatedAt;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Choice(callback={"AppBundle\Util\ArticleChoiceConverter", "getValidTypes"})
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Url()
     */
    private $media;

    /**
     * @var string
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min=10
     * )
     */
    private $preview;

    /**
     * @var string
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min=10
     * )
     */
    private $content;

    /**
     * @var integer
     * @ORM\Column(type="bigint")
     */
    private $views;

    /**
     * @var User
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\User",
     *     inversedBy="articles",
     *     cascade={"persist"}
     * )
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=false)
     */
    private $author;

    /**
     * @var Category
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\Category",
     *     inversedBy="articles",
     *     cascade={"persist"}
     * )
     * @ORM\JoinColumn(name="section_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(
     *     targetEntity="AppBundle\Entity\Tag",
     *     cascade={"persist"},
     *     fetch="EXTRA_LAZY"
     * )
     * @ORM\JoinTable(name="article_tag")
     * @ORM\OrderBy({"name": "ASC"})
     * @Assert\Count(max="6")
     */
    private $tags;

    public function __construct()
    {
        $this->addedAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->media = null;
        $this->views = 0;
        $this->tags = new ArrayCollection();
    }

    /**
     * Get id
     * @return integer
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setArticleId(string $uuid): self
    {
        $this->articleId = $uuid;
        return $this;
    }

    public function getArticleId(): ?string
    {
        return $this->articleId;
    }

    /**
     * Set title
     * @param string $title
     * @return Article
     */
    public function setTitle($title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set slug
     * @param string $slug
     * @return Article
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
     * Set addedAt
     * @param \DateTime $addedAt
     * @return Article
     */
    public function setAddedAt($addedAt): self
    {
        $this->addedAt = $addedAt;

        return $this;
    }

    /**
     * Get addedAt
     * @return \DateTime
     */
    public function getAddedAt(): ?\DateTime
    {
        return $this->addedAt;
    }

    /**
     * Set updatedAt
     * @param \DateTime $updatedAt
     * @return Article
     */
    public function setUpdatedAt($updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     * @return \DateTime
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * Set type
     * @param int $type
     * @return Article
     */
    public function setType($type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     * @return int
     */
    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * Set media uuid
     * @param string $media
     * @return Article
     */
    public function setMedia($media): self
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Get media uuid
     * @return string
     */
    public function getMedia(): ?string
    {
        return $this->media;
    }

    /**
     * Set preview
     * @param string $preview
     * @return Article
     */
    public function setPreview($preview): self
    {
        $this->preview = $preview;

        return $this;
    }

    /**
     * Get preview
     * @return string
     */
    public function getPreview(): ?string
    {
        return $this->preview;
    }

    /**
     * Set content
     * @param string $content
     * @return Article
     */
    public function setContent($content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     * @return string
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Set views
     * @param integer $views
     * @return Article
     */
    public function setViews($views): self
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views
     * @return integer
     */
    public function getViews(): ?int
    {
        return $this->views;
    }

    /**
     * Set author
     * @param \AppBundle\Entity\User $author
     * @return Article
     */
    public function setAuthor(User $author = null): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     * @return \AppBundle\Entity\User
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setCategory(Category $category = null): self
    {
        $this->category = $category;
        $category->addArticle($this);

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function addTag(Tag $tag): void
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }
    }

    public function removeTag(Tag $tag): void
    {
        $this->tags->removeElement($tag);
    }

    public function getTags(): ?ArrayCollection
    {
        return $this->tags;
    }
}
