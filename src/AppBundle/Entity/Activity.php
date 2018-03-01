<?php

namespace AppBundle\Entity;

use AppBundle\Library\Slug;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Activity
 *
 * @ORM\Table(name="Activity")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ActivityRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Activity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Company
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company", inversedBy="activities")
     */
    private $company;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Timesheet", mappedBy="activity")
     */
    private $timesheets;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\FavouriteActivity", mappedBy="activity", cascade={"persist", "remove"})
     */
    private $favouriteActivities;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\PrePersist
     */
    public function saveSlug(){
        $slug = Slug::getAlphanumericalSlug($this->getName());
        $this->setSlug($slug);
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->timesheets = new ArrayCollection();
        $this->favouriteActivities = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Activity
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set company
     *
     * @param \AppBundle\Entity\Company $company
     * @return Activity
     */
    public function setCompany(\AppBundle\Entity\Company $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \AppBundle\Entity\Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Add timesheets
     *
     * @param \AppBundle\Entity\Timesheet $timesheets
     * @return Activity
     */
    public function addTimesheet(\AppBundle\Entity\Timesheet $timesheets)
    {
        $this->timesheets[] = $timesheets;

        return $this;
    }

    /**
     * Remove timesheets
     *
     * @param \AppBundle\Entity\Timesheet $timesheets
     */
    public function removeTimesheet(\AppBundle\Entity\Timesheet $timesheets)
    {
        $this->timesheets->removeElement($timesheets);
    }

    /**
     * Get timesheets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTimesheets()
    {
        return $this->timesheets;
    }

    /**
     * Add favouriteActivities
     *
     * @param \AppBundle\Entity\FavouriteActivity $favouriteActivities
     * @return Activity
     */
    public function addFavouriteActivity(\AppBundle\Entity\FavouriteActivity $favouriteActivities)
    {
        $this->favouriteActivities[] = $favouriteActivities;

        return $this;
    }

    /**
     * Remove favouriteActivities
     *
     * @param \AppBundle\Entity\FavouriteActivity $favouriteActivities
     */
    public function removeFavouriteActivity(\AppBundle\Entity\FavouriteActivity $favouriteActivities)
    {
        $this->favouriteActivities->removeElement($favouriteActivities);
    }

    /**
     * Get favouriteActivities
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFavouriteActivities()
    {
        return $this->favouriteActivities;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Activity
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
}
