<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;

/**
 * Timesheet
 *
 * @ORM\Table(name="Timesheet")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\TimesheetRepository")
 */
class Timesheet
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="timesheets")
     */
    private $author;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var Commission
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Commission", inversedBy="timesheets")
     */
    private $commission;

    /**
     * @var YearMonth
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\YearMonth", inversedBy="timesheets")
     */
    private $yearmonth;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $duration = 15;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @var Activity
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Activity", inversedBy="timesheets")
     */
    private $activity;

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
     * Set created
     *
     * @param \DateTime $created
     * @return Timesheet
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Timesheet
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     * @return Timesheet
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer 
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Timesheet
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set author
     *
     * @param \UserBundle\Entity\User $author
     * @return Timesheet
     */
    public function setAuthor(\UserBundle\Entity\User $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \UserBundle\Entity\User 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set commission
     *
     * @param \AppBundle\Entity\Commission $commission
     * @return Timesheet
     */
    public function setCommission(\AppBundle\Entity\Commission $commission = null)
    {
        $this->commission = $commission;

        return $this;
    }

    /**
     * Get commission
     *
     * @return \AppBundle\Entity\Commission 
     */
    public function getCommission()
    {
        return $this->commission;
    }

    /**
     * Set yearmonth
     *
     * @param \AppBundle\Entity\YearMonth $yearmonth
     * @return Timesheet
     */
    public function setYearmonth(\AppBundle\Entity\YearMonth $yearmonth = null)
    {
        $this->yearmonth = $yearmonth;

        return $this;
    }

    /**
     * Get yearmonth
     *
     * @return \AppBundle\Entity\YearMonth 
     */
    public function getYearmonth()
    {
        return $this->yearmonth;
    }

    /**
     * Set activity
     *
     * @param \AppBundle\Entity\Activity $activity
     * @return Timesheet
     */
    public function setActivity(\AppBundle\Entity\Activity $activity = null)
    {
        $this->activity = $activity;

        return $this;
    }

    /**
     * Get activity
     *
     * @return \AppBundle\Entity\Activity 
     */
    public function getActivity()
    {
        return $this->activity;
    }
}
