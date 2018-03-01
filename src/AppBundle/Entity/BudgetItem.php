<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BudgetItem
 *
 * @ORM\Table(name="BudgetItem")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\BudgetItemRepository")
 */
class BudgetItem
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="pricePlan", type="integer", nullable=true)
     */
    private $pricePlan;

    /**
     * @var integer
     *
     * @ORM\Column(name="priceReal", type="integer", nullable=true)
     */
    private $priceReal;

    /**
     * @var YearMonth
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\YearMonth", inversedBy="budgetItemPlans")
     */
    private $yearmonthPlan;

    /**
     * @var YearMonth
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\YearMonth", inversedBy="budgetItemReals")
     */
    private $yearmonthReal;

    /**
     * @var Budget
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Budget", inversedBy="budgetItems", cascade={"persist"})
     */
    private $budget;



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
     * @return BudgetItem
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
     * Set pricePlan
     *
     * @param integer $pricePlan
     * @return BudgetItem
     */
    public function setPricePlan($pricePlan)
    {
        $this->pricePlan = $pricePlan;

        return $this;
    }

    /**
     * Get pricePlan
     *
     * @return integer 
     */
    public function getPricePlan()
    {
        return $this->pricePlan;
    }

    /**
     * Set priceReal
     *
     * @param integer $priceReal
     * @return BudgetItem
     */
    public function setPriceReal($priceReal)
    {
        $this->priceReal = $priceReal;

        return $this;
    }

    /**
     * Get priceReal
     *
     * @return integer 
     */
    public function getPriceReal()
    {
        return $this->priceReal;
    }

    /**
     * Set yearmonthPlan
     *
     * @param \AppBundle\Entity\YearMonth $yearmonthPlan
     * @return BudgetItem
     */
    public function setYearmonthPlan(\AppBundle\Entity\YearMonth $yearmonthPlan = null)
    {
        $this->yearmonthPlan = $yearmonthPlan;

        return $this;
    }

    /**
     * Get yearmonthPlan
     *
     * @return \AppBundle\Entity\YearMonth 
     */
    public function getYearmonthPlan()
    {
        return $this->yearmonthPlan;
    }

    /**
     * Set yearmonthReal
     *
     * @param \AppBundle\Entity\YearMonth $yearmonthReal
     * @return BudgetItem
     */
    public function setYearmonthReal(\AppBundle\Entity\YearMonth $yearmonthReal = null)
    {
        $this->yearmonthReal = $yearmonthReal;

        return $this;
    }

    /**
     * Get yearmonthReal
     *
     * @return \AppBundle\Entity\YearMonth 
     */
    public function getYearmonthReal()
    {
        return $this->yearmonthReal;
    }

    /**
     * Set budget
     *
     * @param \AppBundle\Entity\Budget $budget
     * @return BudgetItem
     */
    public function setBudget(\AppBundle\Entity\Budget $budget = null)
    {
        $this->budget = $budget;

        return $this;
    }

    /**
     * Get budget
     *
     * @return \AppBundle\Entity\Budget 
     */
    public function getBudget()
    {
        return $this->budget;
    }
}
