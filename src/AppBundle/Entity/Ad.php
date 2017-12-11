<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Ad
 * @package AppBundle\Entity
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AdRepository")
 * @ORM\Table(name="ad")
 */
class Ad
{
    const COUNT_HEADER_LEFT_1 = 1;
    const COUNT_HEADER_RIGHT_1 = 1;
    const COUNT_MENU_BOTTOM_1 = 1;
    const COUNT_ARTICLE_RIGHT_1 = 5;
    const COUNT_HOMEPAGE_FEATURED_BOTTOM_1 = 6;
    const COUNT_HOMEPAGE_VIDEO_TOP_1 = 3;
    const COUNT_HOMEPAGE_VIDEO_BOTTOM_1 = 2;

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
    private $uuid;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Choice(callback={"AppBundle\Util\AdChoiceConverter", "getValidPositions"})
     */
    private $position;

    /**
     * @var string
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(max="8192")
     */
    private $message;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Url()
     */
    private $link;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Url()
     */
    private $image;

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
    private $expiredAt;

    /**
     * @var integer
     * @ORM\Column(type="bigint")
     */
    private $views;

    /**
     * @var integer
     * @ORM\Column(type="bigint")
     */
    private $clicks;

    /**
     * @var User
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\User",
     *     inversedBy="ads",
     *     cascade={"PERSIST"},
     *     fetch="EXTRA_LAZY"
     * )
     */
    private $advertiser;

    public function __construct()
    {
        $this->addedAt = new \DateTime();
        $this->expiredAt = new \DateTime('+2 weeks');
        $this->views = 0;
        $this->clicks = 0;
        $this->uuid = Uuid::uuid4()->toString();
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
     * Set uuid
     * @param string $uuid
     * @return Ad
     */
    public function setUuid($uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     * @return string
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * Set position
     * @param string $position
     * @return Ad
     */
    public function setPosition($position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     * @return string
     */
    public function getPosition(): ?string
    {
        return $this->position;
    }

    /**
     * Set title
     * @param string $title
     * @return Ad
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
     * Set message
     * @param string $message
     * @return Ad
     */
    public function setMessage($message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     * @return string
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * Set link
     * @param string $link
     * @return Ad
     */
    public function setLink($link): self
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     * @return string
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * Set image
     * @param string $image
     * @return Ad
     */
    public function setImage($image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     * @return string
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * Set addedAt
     * @param \DateTime $addedAt
     * @return Ad
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
     * Set expiredAt
     * @param \DateTime $expiredAt
     * @return Ad
     */
    public function setExpiredAt($expiredAt): self
    {
        $this->expiredAt = $expiredAt;

        return $this;
    }

    /**
     * Get expiredAt
     * @return \DateTime
     */
    public function getExpiredAt(): ?\DateTime
    {
        return $this->expiredAt;
    }

    /**
     * Set views
     * @param integer $views
     * @return Ad
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
     * Set clicks
     * @param integer $clicks
     * @return Ad
     */
    public function setClicks($clicks): self
    {
        $this->clicks = $clicks;

        return $this;
    }

    /**
     * Get clicks
     * @return integer
     */
    public function getClicks(): ?int
    {
        return $this->clicks;
    }

    /**
     * Set advertiser
     * @param \AppBundle\Entity\User $advertiser
     * @return Ad
     */
    public function setAdvertiser(User $advertiser = null): self
    {
        $this->advertiser = $advertiser;

        return $this;
    }

    /**
     * Get advertiser
     * @return \AppBundle\Entity\User
     */
    public function getAdvertiser(): ?User
    {
        return $this->advertiser;
    }
}
