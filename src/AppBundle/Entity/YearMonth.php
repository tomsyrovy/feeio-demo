<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * YearMonth
 *
 * @ORM\Table(name="YearMonth")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\YearMonthRepository")
 */
class YearMonth
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
     * @var integer
     *
     * @ORM\Column(name="year", type="integer")
     */
    private $year;

    /**
     * @var integer
     *
     * @ORM\Column(name="month", type="integer")
     */
    private $month;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Subcommission", mappedBy="yearmonth")
     */
    private $subcommissions;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\BudgetItem", mappedBy="yearmonthPlan")
     */
    private $budgetItemPlans;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\BudgetItem", mappedBy="yearmonthReal")
     */
    private $budgetItemReals;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Timesheet", mappedBy="yearmonth")
     */
    private $timesheets;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Cost", mappedBy="yearmonthPlan")
     */
    private $costPlans;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Cost", mappedBy="yearmonthReal")
     */
    private $costReals;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\AllocationUnit", mappedBy="yearMonth")
     */
    private $allocationUnits;


    public function getCode(){

        return $this->getYear().'-'.str_pad($this->getMonth(), 2, '0', STR_PAD_LEFT);

    }

    public function getShortCode(){

        return substr($this->getYear(), -2).str_pad($this->getMonth(), 2, '0', STR_PAD_LEFT);

    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->subcommissions = new ArrayCollection();
        $this->budgetItemPlans = new ArrayCollection();
        $this->budgetItemReals = new ArrayCollection();
        $this->timesheets = new ArrayCollection();
        $this->costPlans = new ArrayCollection();
        $this->costReals = new ArrayCollection();
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
     * Set year
     *
     * @param integer $year
     * @return YearMonth
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set month
     *
     * @param integer $month
     * @return YearMonth
     */
    public function setMonth($month)
    {
        $this->month = $month;

        return $this;
    }

    /**
     * Get month
     *
     * @return integer 
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Add subcommissions
     *
     * @param \AppBundle\Entity\Subcommission $subcommissions
     * @return YearMonth
     */
    public function addSubcommission(\AppBundle\Entity\Subcommission $subcommissions)
    {
        $this->subcommissions[] = $subcommissions;

        return $this;
    }

    /**
     * Remove subcommissions
     *
     * @param \AppBundle\Entity\Subcommission $subcommissions
     */
    public function removeSubcommission(\AppBundle\Entity\Subcommission $subcommissions)
    {
        $this->subcommissions->removeElement($subcommissions);
    }

    /**
     * Get subcommissions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSubcommissions()
    {
        return $this->subcommissions->filter(
            function($entry) {
                return $entry->getEnabled();
            }
        );
    }

    /**
     * Add budgetItemPlans
     *
     * @param \AppBundle\Entity\BudgetItem $budgetItemPlans
     * @return YearMonth
     */
    public function addBudgetItemPlan(\AppBundle\Entity\BudgetItem $budgetItemPlans)
    {
        $this->budgetItemPlans[] = $budgetItemPlans;

        return $this;
    }

    /**
     * Remove budgetItemPlans
     *
     * @param \AppBundle\Entity\BudgetItem $budgetItemPlans
     */
    public function removeBudgetItemPlan(\AppBundle\Entity\BudgetItem $budgetItemPlans)
    {
        $this->budgetItemPlans->removeElement($budgetItemPlans);
    }

    /**
     * Get budgetItemPlans
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBudgetItemPlans()
    {
        return $this->budgetItemPlans;
    }

    /**
     * Add budgetItemReals
     *
     * @param \AppBundle\Entity\BudgetItem $budgetItemReals
     * @return YearMonth
     */
    public function addBudgetItemReal(\AppBundle\Entity\BudgetItem $budgetItemReals)
    {
        $this->budgetItemReals[] = $budgetItemReals;

        return $this;
    }

    /**
     * Remove budgetItemReals
     *
     * @param \AppBundle\Entity\BudgetItem $budgetItemReals
     */
    public function removeBudgetItemReal(\AppBundle\Entity\BudgetItem $budgetItemReals)
    {
        $this->budgetItemReals->removeElement($budgetItemReals);
    }

    /**
     * Get budgetItemReals
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBudgetItemReals()
    {
        return $this->budgetItemReals;
    }

    /**
     * Add timesheets
     *
     * @param \AppBundle\Entity\Timesheet $timesheets
     * @return YearMonth
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
     * Add costPlans
     *
     * @param \AppBundle\Entity\Cost $costPlans
     * @return YearMonth
     */
    public function addCostPlan(\AppBundle\Entity\Cost $costPlans)
    {
        $this->costPlans[] = $costPlans;

        return $this;
    }

    /**
     * Remove costPlans
     *
     * @param \AppBundle\Entity\Cost $costPlans
     */
    public function removeCostPlan(\AppBundle\Entity\Cost $costPlans)
    {
        $this->costPlans->removeElement($costPlans);
    }

    /**
     * Get costPlans
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCostPlans()
    {
        return $this->costPlans;
    }

    /**
     * Add costReals
     *
     * @param \AppBundle\Entity\Cost $costReals
     * @return YearMonth
     */
    public function addCostReal(\AppBundle\Entity\Cost $costReals)
    {
        $this->costReals[] = $costReals;

        return $this;
    }

    /**
     * Remove costReals
     *
     * @param \AppBundle\Entity\Cost $costReals
     */
    public function removeCostReal(\AppBundle\Entity\Cost $costReals)
    {
        $this->costReals->removeElement($costReals);
    }

    /**
     * Get costReals
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCostReals()
    {
        return $this->costReals;
    }

    /**
     * Add allocationUnits
     *
     * @param \AppBundle\Entity\AllocationUnit $allocationUnits
     * @return YearMonth
     */
    public function addAllocationUnit(\AppBundle\Entity\AllocationUnit $allocationUnits)
    {
        $this->allocationUnits[] = $allocationUnits;

        return $this;
    }

    /**
     * Remove allocationUnits
     *
     * @param \AppBundle\Entity\AllocationUnit $allocationUnits
     */
    public function removeAllocationUnit(\AppBundle\Entity\AllocationUnit $allocationUnits)
    {
        $this->allocationUnits->removeElement($allocationUnits);
    }

    /**
     * Get allocationUnits
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAllocationUnits()
    {
        return $this->allocationUnits;
    }
}
