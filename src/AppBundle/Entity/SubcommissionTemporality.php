<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SubcommissionTemporality
 *
 * @ORM\Table(name="SubcommissionTemporality")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\SubcommissionTemporalityRepository")
 */
class SubcommissionTemporality
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
     * @var string
     *
     * @ORM\Column(name="feeFixPlan", type="decimal", nullable=true)
     */
    private $feeFixPlan = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="feeFixReal", type="decimal", nullable=true)
     */
    private $feeFixReal = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="feeSuccessPlan", type="decimal", nullable=true)
     */
    private $feeSuccessPlan = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="feeSuccessReal", type="decimal", nullable=true)
     */
    private $feeSuccessReal = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="hoursPlan", type="decimal", nullable=true)
     */
    private $hoursPlan = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="hoursReal", type="decimal", nullable=true)
     */
    private $hoursReal = 0;

    /**
     * @var Subcommission
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Subcommission", inversedBy="temporalities", cascade={"persist", "remove"})
     */
    private $subcommission;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_from", type="datetime")
     */
    private $dateFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_until", type="datetime", nullable=true)
     */
    private $dateUntil;




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
     * Set feeFixPlan
     *
     * @param string $feeFixPlan
     * @return SubcommissionTemporality
     */
    public function setFeeFixPlan($feeFixPlan)
    {
        $this->feeFixPlan = $feeFixPlan;

        return $this;
    }

    /**
     * Get feeFixPlan
     *
     * @return string 
     */
    public function getFeeFixPlan()
    {
        return $this->feeFixPlan;
    }

    /**
     * Set feeFixReal
     *
     * @param string $feeFixReal
     * @return SubcommissionTemporality
     */
    public function setFeeFixReal($feeFixReal)
    {
        $this->feeFixReal = $feeFixReal;

        return $this;
    }

    /**
     * Get feeFixReal
     *
     * @return string 
     */
    public function getFeeFixReal()
    {
        return $this->feeFixReal;
    }

    /**
     * Set feeSuccessPlan
     *
     * @param string $feeSuccessPlan
     * @return SubcommissionTemporality
     */
    public function setFeeSuccessPlan($feeSuccessPlan)
    {
        $this->feeSuccessPlan = $feeSuccessPlan;

        return $this;
    }

    /**
     * Get feeSuccessPlan
     *
     * @return string 
     */
    public function getFeeSuccessPlan()
    {
        return $this->feeSuccessPlan;
    }

    /**
     * Set feeSuccessReal
     *
     * @param string $feeSuccessReal
     * @return SubcommissionTemporality
     */
    public function setFeeSuccessReal($feeSuccessReal)
    {
        $this->feeSuccessReal = $feeSuccessReal;

        return $this;
    }

    /**
     * Get feeSuccessReal
     *
     * @return string 
     */
    public function getFeeSuccessReal()
    {
        return $this->feeSuccessReal;
    }

    /**
     * Set hoursPlan
     *
     * @param string $hoursPlan
     * @return SubcommissionTemporality
     */
    public function setHoursPlan($hoursPlan)
    {
        $this->hoursPlan = $hoursPlan;

        return $this;
    }

    /**
     * Get hoursPlan
     *
     * @return string 
     */
    public function getHoursPlan()
    {
        return $this->hoursPlan;
    }

    /**
     * Set hoursReal
     *
     * @param string $hoursReal
     * @return SubcommissionTemporality
     */
    public function setHoursReal($hoursReal)
    {
        $this->hoursReal = $hoursReal;

        return $this;
    }

    /**
     * Get hoursReal
     *
     * @return string 
     */
    public function getHoursReal()
    {
        return $this->hoursReal;
    }

    /**
     * Set dateFrom
     *
     * @param \DateTime $dateFrom
     * @return SubcommissionTemporality
     */
    public function setDateFrom($dateFrom)
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    /**
     * Get dateFrom
     *
     * @return \DateTime 
     */
    public function getDateFrom()
    {
        return $this->dateFrom;
    }

    /**
     * Set dateUntil
     *
     * @param \DateTime $dateUntil
     * @return SubcommissionTemporality
     */
    public function setDateUntil($dateUntil)
    {
        $this->dateUntil = $dateUntil;

        return $this;
    }

    /**
     * Get dateUntil
     *
     * @return \DateTime 
     */
    public function getDateUntil()
    {
        return $this->dateUntil;
    }

    /**
     * Set subcommission
     *
     * @param \AppBundle\Entity\Subcommission $subcommission
     * @return SubcommissionTemporality
     */
    public function setSubcommission(\AppBundle\Entity\Subcommission $subcommission = null)
    {
        $this->subcommission = $subcommission;

        return $this;
    }

    /**
     * Get subcommission
     *
     * @return \AppBundle\Entity\Subcommission 
     */
    public function getSubcommission()
    {
        return $this->subcommission;
    }
}
