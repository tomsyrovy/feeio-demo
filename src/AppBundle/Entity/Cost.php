<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;

/**
 * Cost
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\CostRepository")
 */
class Cost
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
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @var Contact
     *
     * @ORM\ManyToOne(targetEntity="Contact", inversedBy="costs")
     */
    private $supplier;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $receivedDocumentNumber;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $issuedDocumentNumber;

    /**
     * @var Commission
     *
     * @ORM\ManyToOne(targetEntity="Commission", inversedBy="costs")
     */
    private $commission;

    /**
     * @var string
     *
     * @ORM\Column(type="decimal", nullable=true)
     */
    private $priceNonVatPlan;

    /**
     * @var string
     *
     * @ORM\Column(type="decimal", nullable=true)
     */
    private $vatRatePlan;

    /**
     * @var string
     *
     * @ORM\Column(type="decimal", nullable=true)
     */
    private $rebillingPriceNonVatPlan;

    /**
     * @var string
     *
     * @ORM\Column(type="decimal", nullable=true)
     */
    private $rebillingVatRatePlan;

    /**
     * @var YearMonth
     *
     * @ORM\ManyToOne(targetEntity="YearMonth", inversedBy="costPlans")
     */
    private $yearmonthPlan;

    /**
     * @var string
     *
     * @ORM\Column(type="decimal", nullable=true)
     */
    private $priceNonVatReal;

    /**
     * @var string
     *
     * @ORM\Column(type="decimal", nullable=true)
     */
    private $vatRateReal;

    /**
     * @var string
     *
     * @ORM\Column(type="decimal", nullable=true)
     */
    private $rebillingPriceNonVatReal;

    /**
     * @var string
     *
     * @ORM\Column(type="decimal", nullable=true)
     */
    private $rebillingVatRateReal;

    /**
     * @var YearMonth
     *
     * @ORM\ManyToOne(targetEntity="YearMonth", inversedBy="costReals")
     */
    private $yearmonthReal;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="costCreateds")
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="costUpdateds")
     */
    private $updatedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated;


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
     * Set priceNonVatPlan
     *
     * @param string $priceNonVatPlan
     * @return Cost
     */
    public function setPriceNonVatPlan($priceNonVatPlan)
    {
        $this->priceNonVatPlan = $priceNonVatPlan;

        return $this;
    }

    /**
     * Get priceNonVatPlan
     *
     * @return string 
     */
    public function getPriceNonVatPlan()
    {
        return $this->priceNonVatPlan;
    }

    /**
     * Set vatRatePlan
     *
     * @param string $vatRatePlan
     * @return Cost
     */
    public function setVatRatePlan($vatRatePlan)
    {
        $this->vatRatePlan = $vatRatePlan;

        return $this;
    }

    /**
     * Get vatRatePlan
     *
     * @return string 
     */
    public function getVatRatePlan()
    {
        return $this->vatRatePlan;
    }

    /**
     * Set rebillingPriceNonVatPlan
     *
     * @param string $rebillingPriceNonVatPlan
     * @return Cost
     */
    public function setRebillingPriceNonVatPlan($rebillingPriceNonVatPlan)
    {
        $this->rebillingPriceNonVatPlan = $rebillingPriceNonVatPlan;

        return $this;
    }

    /**
     * Get rebillingPriceNonVatPlan
     *
     * @return string 
     */
    public function getRebillingPriceNonVatPlan()
    {
        return $this->rebillingPriceNonVatPlan;
    }

    /**
     * Set rebillingVatRatePlan
     *
     * @param string $rebillingVatRatePlan
     * @return Cost
     */
    public function setRebillingVatRatePlan($rebillingVatRatePlan)
    {
        $this->rebillingVatRatePlan = $rebillingVatRatePlan;

        return $this;
    }

    /**
     * Get rebillingVatRatePlan
     *
     * @return string 
     */
    public function getRebillingVatRatePlan()
    {
        return $this->rebillingVatRatePlan;
    }

    /**
     * Set priceNonVatReal
     *
     * @param string $priceNonVatReal
     * @return Cost
     */
    public function setPriceNonVatReal($priceNonVatReal)
    {
        $this->priceNonVatReal = $priceNonVatReal;

        return $this;
    }

    /**
     * Get priceNonVatReal
     *
     * @return string 
     */
    public function getPriceNonVatReal()
    {
        return $this->priceNonVatReal;
    }

    /**
     * Set vatRateReal
     *
     * @param string $vatRateReal
     * @return Cost
     */
    public function setVatRateReal($vatRateReal)
    {
        $this->vatRateReal = $vatRateReal;

        return $this;
    }

    /**
     * Get vatRateReal
     *
     * @return string 
     */
    public function getVatRateReal()
    {
        return $this->vatRateReal;
    }

    /**
     * Set rebillingPriceNonVatReal
     *
     * @param string $rebillingPriceNonVatReal
     * @return Cost
     */
    public function setRebillingPriceNonVatReal($rebillingPriceNonVatReal)
    {
        $this->rebillingPriceNonVatReal = $rebillingPriceNonVatReal;

        return $this;
    }

    /**
     * Get rebillingPriceNonVatReal
     *
     * @return string 
     */
    public function getRebillingPriceNonVatReal()
    {
        return $this->rebillingPriceNonVatReal;
    }

    /**
     * Set rebillingVatRateReal
     *
     * @param string $rebillingVatRateReal
     * @return Cost
     */
    public function setRebillingVatRateReal($rebillingVatRateReal)
    {
        $this->rebillingVatRateReal = $rebillingVatRateReal;

        return $this;
    }

    /**
     * Get rebillingVatRateReal
     *
     * @return string 
     */
    public function getRebillingVatRateReal()
    {
        return $this->rebillingVatRateReal;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Cost
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
     * Set updated
     *
     * @param \DateTime $updated
     * @return Cost
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set commission
     *
     * @param \AppBundle\Entity\Commission $commission
     * @return Cost
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
     * Set yearmonthPlan
     *
     * @param \AppBundle\Entity\YearMonth $yearmonthPlan
     * @return Cost
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
     * @return Cost
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
     * Set createdBy
     *
     * @param \UserBundle\Entity\User $createdBy
     * @return Cost
     */
    public function setCreatedBy(\UserBundle\Entity\User $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \UserBundle\Entity\User 
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set updatedBy
     *
     * @param \UserBundle\Entity\User $updatedBy
     * @return Cost
     */
    public function setUpdatedBy(\UserBundle\Entity\User $updatedBy = null)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return \UserBundle\Entity\User 
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Set receivedDocumentNumber
     *
     * @param string $receivedDocumentNumber
     * @return Cost
     */
    public function setReceivedDocumentNumber($receivedDocumentNumber)
    {
        $this->receivedDocumentNumber = $receivedDocumentNumber;

        return $this;
    }

    /**
     * Get receivedDocumentNumber
     *
     * @return string 
     */
    public function getReceivedDocumentNumber()
    {
        return $this->receivedDocumentNumber;
    }

    /**
     * Set issuedDocumentNumber
     *
     * @param string $issuedDocumentNumber
     * @return Cost
     */
    public function setIssuedDocumentNumber($issuedDocumentNumber)
    {
        $this->issuedDocumentNumber = $issuedDocumentNumber;

        return $this;
    }

    /**
     * Get issuedDocumentNumber
     *
     * @return string 
     */
    public function getIssuedDocumentNumber()
    {
        return $this->issuedDocumentNumber;
    }

    /**
     * Set supplier
     *
     * @param \AppBundle\Entity\Contact $supplier
     * @return Cost
     */
    public function setSupplier(\AppBundle\Entity\Contact $supplier = null)
    {
        $this->supplier = $supplier;

        return $this;
    }

    /**
     * Get supplier
     *
     * @return \AppBundle\Entity\Contact 
     */
    public function getSupplier()
    {
        return $this->supplier;
    }

    /**
     * @return string
     */
    public function getTitle(){
        return $this->title;
    }

    /**
     * @param $title
     *
     * @return $this
     */
    public function setTitle( $title ){
        $this->title = $title;

        return $this;
    }



}
