<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Budget
 *
 * @ORM\Table(name="Budget")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\BudgetRepository")
 */
class Budget
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Commission", inversedBy="budgets")
     */
    private $commission;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var UserCompany
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserCompany", inversedBy="budgets")
     */
    private $author;

    /**
     * @var integer
     *
     * @ORM\Column(name="version", type="integer")
     */
    private $version;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\BudgetItem", mappedBy="budget", cascade={"persist"})
     */
    private $budgetItems;

    /**
     * @return int
     */
    public function getPriceSumPlan(){

        $sum = 0;
        foreach($this->getBudgetItems() as $bi){

            $sum += $bi->getPricePlan();

        }

        return $sum;

    }

    /**
     * @return int
     */
    public function getPriceSumReal(){

        $sum = 0;
        foreach($this->getBudgetItems() as $bi){

            $sum += $bi->getPriceReal();

        }

        return $sum;

    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->budgetItems = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set created
     *
     * @param \DateTime $created
     * @return Budget
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
     * Set version
     *
     * @param integer $version
     * @return Budget
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return integer 
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Budget
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
     * Set commission
     *
     * @param \AppBundle\Entity\Commission $commission
     * @return Budget
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
     * Set author
     *
     * @param \AppBundle\Entity\UserCompany $author
     * @return Budget
     */
    public function setAuthor(\AppBundle\Entity\UserCompany $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \AppBundle\Entity\UserCompany 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Add budgetItems
     *
     * @param \AppBundle\Entity\BudgetItem $budgetItems
     * @return Budget
     */
    public function addBudgetItem(\AppBundle\Entity\BudgetItem $budgetItems)
    {
        $this->budgetItems[] = $budgetItems;
        $budgetItems->setBudget($this);

        return $this;
    }

    /**
     * Remove budgetItems
     *
     * @param \AppBundle\Entity\BudgetItem $budgetItems
     */
    public function removeBudgetItem(\AppBundle\Entity\BudgetItem $budgetItems)
    {
        $this->budgetItems->removeElement($budgetItems);
    }

    /**
     * Get budgetItems
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBudgetItems()
    {
        return $this->budgetItems;
    }
}
