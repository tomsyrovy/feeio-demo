<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * AllocationContainerListItem
 *
 * @ORM\Table(name="AllocationContainerListItem")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\AllocationContainerListItemRepository")
 */
class AllocationContainerListItem
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
     * @var AllocationContainerList
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AllocationContainerList", inversedBy="allocationContainerListItems", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $allocationContainerList;

    /**
     * @var boolean
     *
     * @ORM\Column(name="external_source", type="boolean")
     */
    private $externalSource = true;

    /**
     * @var Source
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Source")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $generalSource;

    /**
     * @var UserCompany
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserCompany")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $concreteSource;

    /**
     * @var string
     *
     * @ORM\Column(name="generalSourceExt", type="string", length=255, nullable=true)
     */
    private $generalSourceExt;

    /**
     * @var string
     *
     * @ORM\Column(name="concreteSourceExt", type="string", length=255, nullable=true)
     */
    private $concreteSourceExt;

    /**
     * @var string
     *
     * @ORM\Column(name="unit", type="string", length=255, nullable=true)
     */
    private $unit;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantityPlan", type="integer", nullable=true)
     */
    private $quantityPlan;

    /**
     * @var float
     *
     * @ORM\Column(name="buyingPricePlan", type="float", nullable=true)
     */
    private $buyingPricePlan;

    /**
     * @var float
     *
     * @ORM\Column(name="sellingPricePlan", type="float", nullable=true)
     */
    private $sellingPricePlan;

    /**
     * @var float
     *
     * @ORM\Column(name="quantityReal", type="float", nullable=true)
     */
    private $quantityReal;

    /**
     * @var float
     *
     * @ORM\Column(name="byuingPriceReal", type="float", nullable=true)
     */
    private $byuingPriceReal;

    /**
     * @var float
     *
     * @ORM\Column(name="buyingPriceReal", type="float", nullable=true)
     */
    private $buyingPriceReal;

    /**
     * @var float
     *
     * @ORM\Column(name="sellingPriceReal", type="float", nullable=true)
     */
    private $sellingPriceReal;

    public function __clone(){
        if($this->id){
            $this->id = null;
        }
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
     * Set generalSource
     *
     * @param Source $generalSource
     * @return AllocationContainerListItem
     */
    public function setGeneralSource(Source $generalSource)
    {
        $this->generalSource = $generalSource;

        return $this;
    }

    /**
     * Get generalSource
     *
     * @return Source
     */
    public function getGeneralSource()
    {
        return $this->generalSource;
    }

    /**
     * Set concreteSource
     *
     * @param string $concreteSource
     * @return AllocationContainerListItem
     */
    public function setConcreteSource($concreteSource)
    {
        $this->concreteSource = $concreteSource;

        return $this;
    }

    /**
     * Get concreteSource
     *
     * @return UserCompany
     */
    public function getConcreteSource()
    {
        return $this->concreteSource;
    }

    /**
     * Set unit
     *
     * @param string $unit
     * @return AllocationContainerListItem
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * Get unit
     *
     * @return string 
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Set quantityPlan
     *
     * @param integer $quantityPlan
     * @return AllocationContainerListItem
     */
    public function setQuantityPlan($quantityPlan)
    {
        $this->quantityPlan = $quantityPlan;

        return $this;
    }

    /**
     * Get quantityPlan
     *
     * @return integer 
     */
    public function getQuantityPlan()
    {
        return $this->quantityPlan;
    }

    /**
     * Set buyingPricePlan
     *
     * @param float $buyingPricePlan
     * @return AllocationContainerListItem
     */
    public function setBuyingPricePlan($buyingPricePlan)
    {
        $this->buyingPricePlan = $buyingPricePlan;

        return $this;
    }

    /**
     * Get buyingPricePlan
     *
     * @return float 
     */
    public function getBuyingPricePlan()
    {
        return $this->buyingPricePlan;
    }

    /**
     * Set sellingPricePlan
     *
     * @param float $sellingPricePlan
     * @return AllocationContainerListItem
     */
    public function setSellingPricePlan($sellingPricePlan)
    {
        $this->sellingPricePlan = $sellingPricePlan;

        return $this;
    }

    /**
     * Get sellingPricePlan
     *
     * @return float 
     */
    public function getSellingPricePlan()
    {
        return $this->sellingPricePlan;
    }

    /**
     * Set quantityReal
     *
     * @param integer $quantityReal
     * @return AllocationContainerListItem
     */
    public function setQuantityReal($quantityReal)
    {
        $this->quantityReal = $quantityReal;

        return $this;
    }

    /**
     * Get quantityReal
     *
     * @return integer 
     */
    public function getQuantityReal()
    {
        if($this->getUnit() === 'h' and $this->getConcreteSource()){
            $allocationContainerList = $this->getAllocationContainerList();
            if($allocationContainerList){
                $allocationContainer = $allocationContainerList->getAllocationContainer();
                if($allocationContainer){
                    $commission = $allocationContainer->getCommission();
                    if($commission){
                        $timesheets = $commission->getTimesheets();
                        $sum = 0;
                        /** @var Timesheet $timesheet */
                        foreach($timesheets as $timesheet){
                            if($timesheet->getAuthor() === $this->concreteSource->getUser()){
                                $sum = $sum+($timesheet->getDuration()/60);
                            }
                        }
                        return round($sum*100)/100;
                    }
                }
            }
        }else{
            return $this->quantityReal;
        }
    }

    /**
     * Set byuingPriceReal
     *
     * @param float $byuingPriceReal
     * @return AllocationContainerListItem
     */
    public function setByuingPriceReal($byuingPriceReal)
    {
        $this->byuingPriceReal = $byuingPriceReal;

        return $this;
    }

    /**
     * Get byuingPriceReal
     *
     * @return float 
     */
    public function getByuingPriceReal()
    {
        return $this->byuingPriceReal;
    }

    /**
     * Set buyingPriceReal
     *
     * @param float $buyingPriceReal
     * @return AllocationContainerListItem
     */
    public function setBuyingPriceReal($buyingPriceReal)
    {
        $this->buyingPriceReal = $buyingPriceReal;

        return $this;
    }

    /**
     * Get buyingPriceReal
     *
     * @return float 
     */
    public function getBuyingPriceReal()
    {
        return $this->buyingPriceReal;
    }

    /**
     * Set sellingPriceReal
     *
     * @param float $sellingPriceReal
     * @return AllocationContainerListItem
     */
    public function setSellingPriceReal($sellingPriceReal)
    {
        $this->sellingPriceReal = $sellingPriceReal;

        return $this;
    }

    /**
     * Get sellingPriceReal
     *
     * @return float 
     */
    public function getSellingPriceReal()
    {
        return $this->sellingPriceReal;
    }

    /**
     * Set allocationContainerList
     *
     * @param \AppBundle\Entity\AllocationContainerList $allocationContainerList
     * @return AllocationContainerListItem
     */
    public function setAllocationContainerList(\AppBundle\Entity\AllocationContainerList $allocationContainerList = null)
    {
        $this->allocationContainerList = $allocationContainerList;

        return $this;
    }

    /**
     * Get allocationContainerList
     *
     * @return \AppBundle\Entity\AllocationContainerList 
     */
    public function getAllocationContainerList()
    {
        return $this->allocationContainerList;
    }

    /**
     * Set externalSource
     *
     * @param boolean $externalSource
     * @return AllocationContainerListItem
     */
    public function setExternalSource($externalSource)
    {
        $this->externalSource = $externalSource;

        return $this;
    }

    /**
     * Get externalSource
     *
     * @return boolean 
     */
    public function getExternalSource()
    {
        return $this->externalSource;
    }

    /**
     * Set generalSourceExt
     *
     * @param string $generalSourceExt
     * @return AllocationContainerListItem
     */
    public function setGeneralSourceExt($generalSourceExt)
    {
        $this->generalSourceExt = $generalSourceExt;

        return $this;
    }

    /**
     * Get generalSourceExt
     *
     * @return string 
     */
    public function getGeneralSourceExt()
    {
        return $this->generalSourceExt;
    }

    /**
     * Set concreteSourceExt
     *
     * @param string $concreteSourceExt
     * @return AllocationContainerListItem
     */
    public function setConcreteSourceExt($concreteSourceExt)
    {
        $this->concreteSourceExt = $concreteSourceExt;

        return $this;
    }

    /**
     * Get concreteSourceExt
     *
     * @return string 
     */
    public function getConcreteSourceExt()
    {
        return $this->concreteSourceExt;
    }
}
