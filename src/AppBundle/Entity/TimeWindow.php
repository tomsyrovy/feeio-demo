<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;

/**
 * TimeWindow
 *
 * @ORM\Table(name="TimeWindow")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\TimeWindowRepository")
 */
class TimeWindow
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
     * @ORM\OneToOne(targetEntity="UserBundle\Entity\User", inversedBy="timeWindow")
     */
    private $user;

    /**
     * @var YearMonth
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\YearMonth")
     */
    private $yearmonthFrom;

    /**
     * @var YearMonth
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\YearMonth")
     */
    private $yearmonthTo;



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
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     * @return TimeWindow
     */
    public function setUser(\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set yearmonthFrom
     *
     * @param \AppBundle\Entity\YearMonth $yearmonthFrom
     * @return TimeWindow
     */
    public function setYearmonthFrom(\AppBundle\Entity\YearMonth $yearmonthFrom = null)
    {
        $this->yearmonthFrom = $yearmonthFrom;

        return $this;
    }

    /**
     * Get yearmonthFrom
     *
     * @return \AppBundle\Entity\YearMonth 
     */
    public function getYearmonthFrom()
    {
        return $this->yearmonthFrom;
    }

    /**
     * Set yearmonthTo
     *
     * @param \AppBundle\Entity\YearMonth $yearmonthTo
     * @return TimeWindow
     */
    public function setYearmonthTo(\AppBundle\Entity\YearMonth $yearmonthTo = null)
    {
        $this->yearmonthTo = $yearmonthTo;

        return $this;
    }

    /**
     * Get yearmonthTo
     *
     * @return \AppBundle\Entity\YearMonth 
     */
    public function getYearmonthTo()
    {
        return $this->yearmonthTo;
    }
}
