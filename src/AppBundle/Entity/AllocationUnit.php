<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AllocationUnit
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\AllocationUnitRepository")
 */
class AllocationUnit
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
     * @var Commission
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Commission", inversedBy="allocationUnits", cascade={"persist", "remove"})
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $commission;

    /**
     * @var integer
     */
    private $freeHours;

    /**
     * @var UserCompany
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserCompany", inversedBy="allocationUnits", cascade={"persist"})
     */
    private $userCompany;

    /**
     * @var YearMonth
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\YearMonth", inversedBy="allocationUnits", cascade={"persist",
     *                                                           "remove"})
     */
    private $yearMonth;

    /**
     * @var string
     *
     * @ORM\Column(name="hoursPlan", type="integer")
     */
    private $hoursPlan;

    /**
     * @var string
     *
     * @ORM\Column(name="hoursReal", type="float", nullable=true)
     */
    private $hoursReal;


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
     * Set hoursPlan
     *
     * @param integer $hoursPlan
     * @return AllocationUnit
     */
    public function setHoursPlan($hoursPlan)
    {
        $this->hoursPlan = $hoursPlan;

        return $this;
    }

    /**
     * Get hoursPlan
     *
     * @return integer 
     */
    public function getHoursPlan()
    {
        return $this->hoursPlan;
    }

    /**
     * Set hoursReal
     *
     * @param integer $hoursReal
     * @return AllocationUnit
     */
    public function setHoursReal($hoursReal)
    {
        $this->hoursReal = $hoursReal;

        return $this;
    }

    /**
     * Get hoursReal
     *
     * @return integer 
     */
    public function getHoursReal()
    {
        $sum = 0;
        /** @var Timesheet $timesheet */
        foreach($this->getCommission()->getTimesheets() as $timesheet){
            if($timesheet->getYearmonth() === $this->getYearMonth() and $timesheet->getAuthor() === $this->getUserCompany()->getUser()){
                $sum += $timesheet->getDuration();
            }
        }

        return round($sum/60*100)/100;

    }

    /**
     * Set commission
     *
     * @param \AppBundle\Entity\Commission $commission
     * @return AllocationUnit
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
     * Set yearMonth
     *
     * @param \AppBundle\Entity\YearMonth $yearMonth
     * @return AllocationUnit
     */
    public function setYearMonth(\AppBundle\Entity\YearMonth $yearMonth = null)
    {
        $this->yearMonth = $yearMonth;

        return $this;
    }

    /**
     * Get yearMonth
     *
     * @return \AppBundle\Entity\YearMonth 
     */
    public function getYearMonth()
    {
        return $this->yearMonth;
    }

    /**
     * Set userCompany
     *
     * @param \AppBundle\Entity\UserCompany $userCompany
     * @return AllocationUnit
     */
    public function setUserCompany(\AppBundle\Entity\UserCompany $userCompany = null)
    {
        $this->userCompany = $userCompany;

        return $this;
    }

    /**
     * Get userCompany
     *
     * @return \AppBundle\Entity\UserCompany 
     */
    public function getUserCompany()
    {
        return $this->userCompany;
    }

    /**
     * @return int
     */
    public function getFreeHours(){
        return $this->freeHours;
    }

    /**
     * @param int $freeHours
     */
    public function setFreeHours( $freeHours ){
        $this->freeHours = $freeHours;
    }
}
