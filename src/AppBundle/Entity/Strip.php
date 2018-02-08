<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Strip
 *
 * @ORM\Table(name="strip")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StripRepository")
 */
class Strip
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creationDate", type="datetime")
     */
    private $creationDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="publicationDate", type="datetime")
     */
    private $publicationDate;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, unique=true)
     */
    private $title;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"title"}, updatable=false)
     * @ORM\Column(name="slug", type="string", length=300, unique=true)
     */
    protected $slug;

    /**
     * @var array
     * 
     * @ORM\ManyToMany(targetEntity="ContentWarning", inversedBy="strips")
     * @ORM\JoinTable(name="strips_contentWarnings")
     */
    private $contentWarnings;

    
    public function __construct() {
        $this->contentWarnings = new ArrayCollection();
    }
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="Account", inversedBy="strips")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
     */
    private $author;

    /**
     * @var array
     *
     * @ORM\Column(name="stripElements", type="array")
     * @Assert\NotBlank(message="Oops, seems like you forgot to upload the file(s).")
     */
    private $stripElements;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return Strip
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set publicationDate
     *
     * @param \DateTime $publicationDate
     *
     * @return Strip
     */
    public function setPublicationDate($publicationDate)
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    /**
     * Get publicationDate
     *
     * @return \DateTime
     */
    public function getPublicationDate()
    {
        return $this->publicationDate;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Strip
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Strip
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set contentWarnings
     *
     * @param array $contentWarnings
     *
     * @return Strip
     */
    public function setContentWarnings($contentWarnings)
    {
        $this->contentWarnings = $contentWarnings;

        return $this;
    }

    /**
     * Get contentWarnings
     *
     * @return array
     */
    public function getContentWarnings()
    {
        return $this->contentWarnings;
    }

    /**
     * Set author
     *
     * @param \stdClass $author
     *
     * @return Strip
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \stdClass
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set stripElements
     *
     * @param array $stripElements
     *
     * @return Strip
     */
    public function setStripElements($stripElements)
    {
        $this->stripElements = $stripElements;

        return $this;
    }

    /**
     * Get stripElements
     *
     * @return array
     */
    public function getStripElements()
    {
        return $this->stripElements;
    }
}

