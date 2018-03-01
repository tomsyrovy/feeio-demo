<?php

    namespace AppBundle\Entity;

    use AppBundle\DataObject\SumarizationDataObject;
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\ORM\Mapping as ORM;

    /**
     * Commission
     *
     * @ORM\Table(name="Commission")
     * @ORM\Entity(repositoryClass="AppBundle\Entity\CommissionRepository")
     */
    class Commission extends AbstractCommissionHierarchy
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
         * @ORM\Column(name="name_own", type="string", length=255, nullable=true)
         */
        private $nameOwn;

        /**
         * @var string
         *
         * @ORM\Column(name="description", type="text", nullable=true)
         */
        private $description;

        /**
         * @var string
         *
         * @ORM\Column(name="status", type="string", length=255, nullable=true)
         */
        private $status;

        /**
         * @var \DateTime
         *
         * @ORM\Column(type="datetime")
         */
        private $created;

        /**
         * @var Campaign
         *
         * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Campaign", inversedBy="commissions")
         * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
         */
        private $campaign;

        /**
         * @var ArrayCollection
         *
         * @ORM\OneToMany(targetEntity="AppBundle\Entity\CommissionUserCompanyRelation", mappedBy="commission", cascade={"persist"})
         */
        private $commissionUserCompanyRelations;

        /**
         * @var ArrayCollection
         *
         * @ORM\OneToMany(targetEntity="AppBundle\Entity\Subcommission", mappedBy="commission")
         */
        private $subcommissions;

        /**
         * @var ArrayCollection
         *
         * @ORM\OneToMany(targetEntity="AppBundle\Entity\Budget", mappedBy="commission")
         */
        private $budgets;

        /**
         * @var ArrayCollection
         *
         * @ORM\OneToMany(targetEntity="AppBundle\Entity\Timesheet", mappedBy="commission")
         */
        private $timesheets;

        /**
         * @var boolean
         *
         * @ORM\Column(type="boolean")
         */
        private $lockedForTimesheets = false;

        /**
         * @var ArrayCollection
         *
         * @ORM\OneToMany(targetEntity="AppBundle\Entity\FavouriteCommission", mappedBy="commission")
         */
        private $favouriteCommissions;

        /**
         * @var ArrayCollection
         *
         * @ORM\OneToMany(targetEntity="AppBundle\Entity\Cost", mappedBy="commission")
         */
        private $costs;

        /**
         * @var File
         * @ORM\OneToOne(targetEntity="AppBundle\Entity\File", cascade={"persist"})
         * @ORM\JoinColumn(name="file_id", referencedColumnName="id")
         */
        private $image;

        /**
         * @var \DateTime
         *
         * @ORM\ManyToOne(targetEntity="AppBundle\Entity\YearMonth")
         * @ORM\JoinColumn(referencedColumnName="id")
         */
        private $startDate;

        /**
         * @var \DateTime
         *
         * @ORM\ManyToOne(targetEntity="AppBundle\Entity\YearMonth")
         * @ORM\JoinColumn(referencedColumnName="id")
         */
        private $endDate;

        /**
         * @var bool
         *
         * @ORM\Column(name="client_approved", type="boolean")
         */
        private $clientApproved = false;

        /**
         * @var bool
         *
         * @ORM\Column(name="billable", type="boolean")
         */
        private $billable = true;

        /**
         * @var bool
         *
         * @ORM\Column(name="repeatable", type="boolean")
         */
        private $repeatable = false;

        /**
         * @var ArrayCollection
         *
         * @ORM\OneToMany(targetEntity="AppBundle\Entity\AllocationContainer", mappedBy="commission",
         *                                                                     cascade={"persist"})
         */
        private $allocationContainers;

        /**
         * @var string
         *
         * @ORM\Column(name="name", type="string", length=255)
         */
        private $name;

        /**
         * @var ArrayCollection
         *
         * @ORM\OneToMany(targetEntity="AppBundle\Entity\AllocationUnit", mappedBy="commission", cascade={"persist"})
         */
        private $allocationUnits;

        /**
         * @var ArrayCollection
         *
         * @ORM\OneToMany(targetEntity="AppBundle\Entity\InvoiceItem", mappedBy="commission", cascade={"persist",
         *                                                             "remove"})
         */
        private $invoiceItems;

//    /**
//     * @var SumarizationDataObject
//     */
//    private $sums;
//
//    /**
//     * @return SumarizationDataObject
//     */
//    public function getSums(){
//        return $this->sums;
//    }
//
//    /**
//     * @param SumarizationDataObject $sums
//     */
//    public function setSums( $sums ){
//        $this->sums = $sums;
//    }

        public function getInvoiceItemsSum(){
            $items = array_filter($this->getInvoiceItems()->toArray(), function($entry){
                return ($entry->getEnabled()) ? true : false;
            });
            $sum = 0;
            /** @var InvoiceItem $item */
            foreach($items as $item){
                $sum = $sum+$item->getPrice();
            }
            return $sum;
        }

        public function getSums(){

            $array = [];

            /** @var AllocationContainer $item */
            foreach($this->getAllocationContainers() as $item){
                if($item->getEnabled() && $item->getClientApproved()){
                    $array[] = $item->getSums();
                }
            }

            $sums = new AllocationsContainerSums();
            $sums->sumarize($array);
            $sums->setInvoicePrice($this->getInvoiceItemsSum());

//        if($sums->getInvoicePrice() > 0){
//            dump($sums);exit;
//        }

            return $sums;

        }

        public function getNetIncome(){

            $costsBuyingReal = 0;
            $costsSellingReal = 0;

            /** @var AllocationContainer $allocationContainer */
            foreach($this->getAllocationContainers() as $allocationContainer){
                if($allocationContainer->getClientApproved() and $allocationContainer->getEnabled()){
                    /** @var AllocationContainerList $allocationContainerList */
                    foreach($allocationContainer->getAllocationContainerLists() as $allocationContainerList){
                        /** @var AllocationContainerListItem $allocationContainerListItem */
                        foreach($allocationContainerList->getAllocationContainerListItems() as $allocationContainerListItem){
                            if($allocationContainerListItem->getExternalSource()){
                                $costsBuyingReal = $costsBuyingReal + $allocationContainerListItem->getBuyingPriceReal()*$allocationContainerListItem->getQuantityReal();
                                $costsSellingReal = $costsSellingReal + $allocationContainerListItem->getSellingPriceReal()*$allocationContainerListItem->getQuantityReal();
                            }
                        }
                    }
                }
            }

            $r = $this->getInvoiceItemsSum()-$costsBuyingReal+$costsSellingReal;

            return $r;

        }

        public function getHourlyRate(){

            $sum = 0.1;

            $timesheets = $this->getTimesheets();
            /** @var Timesheet $timesheet */
            foreach($timesheets as $timesheet){
                $sum = $sum + $timesheet->getDuration();
            }

            return round($this->getInvoiceItemsSum()/($sum/60));

        }

        public function getHourPlanVsReal(){

            $sumHr = 0.1;

            $timesheets = $this->getTimesheets();
            /** @var Timesheet $timesheet */
            foreach($timesheets as $timesheet){
                $sumHr = $sumHr + $timesheet->getDuration();
            }

            $sumHp = 0.1;
            $allocationUnits = $this->getAllocationUnits();
            /** @var AllocationUnit $allocationUnit */
            foreach($allocationUnits as $allocationUnit){
                $sumHp = $sumHp + $allocationUnit->getHoursPlan();
            }

            return round(($sumHr/60-$sumHp)/($sumHp)*100);

        }

        public function getCompany(){

            return $this->getCampaign()->getClient()->getCompany();

        }

        /**
         * @return string
         */
        public function generateName(){
            $i = $this->campaign->getCommissions()->count()+1;
            $i = str_pad($i, 3, '0', STR_PAD_LEFT);
            return $this->campaign->getName().$i;
        }


        /**
         * Vrátí všechny subzakázky zakázky v definovaném časovém okně
         *
         * @param \AppBundle\Entity\TimeWindow $timeWindow
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getSubcommissionsInTimeWindow(TimeWindow $timeWindow){

            return $this->getSubcommissions()->filter(
                function($entry) use ($timeWindow) {
                    $id = $entry->getYearmonth()->getId();
                    return ($id <= $timeWindow->getYearmonthTo()->getId() and $id >= $timeWindow->getYearmonthFrom()->getId());
                }
            );

        }

        /**
         * Vrátí poslední subzakázku
         *
         * @return mixed
         */
        public function getLastSubcommission(){

            return $this->getSubcommissions()->last();

        }

        public function getJobPositions(){

            $jobPositions = [];

            /** @var Source $source */
            foreach($this->getCampaign()->getSourceList()->getSources() as $source){
                $jobPositions[] = $source->getJobPosition();
            }

            return $jobPositions;

        }

        public function getUserCompanies(){

            $userCompanies = [];

            /** @var CampaignManager $campaignManager */
            foreach($this->getCampaign()->getCampaignManagers() as $campaignManager){
                if($campaignManager->getJobConsultant()){
                    $userCompanies[] = $campaignManager->getUserCompany();
                }
            }

            uasort($userCompanies, function($a, $b){
                return $a->getUser()->getLastname() > $b->getUser()->getLastname();
            });

            return $userCompanies;

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
         * @return Commission
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
         * Add commissionUserCompanyRelations
         *
         * @param \AppBundle\Entity\CommissionUserCompanyRelation $commissionUserCompanyRelations
         * @return Commission
         */
        public function addCommissionUserCompanyRelation(\AppBundle\Entity\CommissionUserCompanyRelation $commissionUserCompanyRelations)
        {
            $this->commissionUserCompanyRelations[] = $commissionUserCompanyRelations;
            $commissionUserCompanyRelations->setCommission($this);

            return $this;
        }

        /**
         * Remove commissionUserCompanyRelations
         *
         * @param \AppBundle\Entity\CommissionUserCompanyRelation $commissionUserCompanyRelations
         */
        public function removeCommissionUserCompanyRelation(\AppBundle\Entity\CommissionUserCompanyRelation $commissionUserCompanyRelations)
        {
            $this->commissionUserCompanyRelations->removeElement($commissionUserCompanyRelations);

        }

        /**
         * Get commissionUserCompanyRelations
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getCommissionUserCompanyRelations()
        {
            return $this->commissionUserCompanyRelations;
        }

        /**
         * Add subcommissions
         *
         * @param \AppBundle\Entity\Subcommission $subcommissions
         * @return Commission
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
         * Add budgets
         *
         * @param \AppBundle\Entity\Budget $budgets
         * @return Commission
         */
        public function addBudget(\AppBundle\Entity\Budget $budgets)
        {
            $this->budgets[] = $budgets;

            return $this;
        }

        /**
         * Remove budgets
         *
         * @param \AppBundle\Entity\Budget $budgets
         */
        public function removeBudget(\AppBundle\Entity\Budget $budgets)
        {
            $this->budgets->removeElement($budgets);
        }

        /**
         * Get budgets
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getBudgets()
        {
            return $this->budgets;
        }

        /**
         * Constructor
         */
        public function __construct()
        {
            $this->commissionUserCompanyRelations = new ArrayCollection();
            $this->subcommissions = new ArrayCollection();
            $this->budgets = new ArrayCollection();
            $this->timesheets = new ArrayCollection();
            $this->favouriteCommissions = new ArrayCollection();
            $this->costs = new ArrayCollection();
        }


        /**
         * Add timesheets
         *
         * @param \AppBundle\Entity\Timesheet $timesheets
         * @return Commission
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
         * Set lockedForTimesheets
         *
         * @param boolean $lockedForTimesheets
         * @return Commission
         */
        public function setLockedForTimesheets($lockedForTimesheets)
        {
            $this->lockedForTimesheets = $lockedForTimesheets;

            return $this;
        }

        /**
         * Get lockedForTimesheets
         *
         * @return boolean
         */
        public function getLockedForTimesheets()
        {
            return $this->lockedForTimesheets;
        }

        /**
         * Add favouriteCommissions
         *
         * @param \AppBundle\Entity\FavouriteCommission $favouriteCommissions
         * @return Commission
         */
        public function addFavouriteCommission(\AppBundle\Entity\FavouriteCommission $favouriteCommissions)
        {
            $this->favouriteCommissions[] = $favouriteCommissions;

            return $this;
        }

        /**
         * Remove favouriteCommissions
         *
         * @param \AppBundle\Entity\FavouriteCommission $favouriteCommissions
         */
        public function removeFavouriteCommission(\AppBundle\Entity\FavouriteCommission $favouriteCommissions)
        {
            $this->favouriteCommissions->removeElement($favouriteCommissions);
        }

        /**
         * Get favouriteCommissions
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getFavouriteCommissions()
        {
            return $this->favouriteCommissions;
        }

        /**
         * Add costs
         *
         * @param \AppBundle\Entity\Cost $costs
         * @return Commission
         */
        public function addCost(\AppBundle\Entity\Cost $costs)
        {
            $this->costs[] = $costs;

            return $this;
        }

        /**
         * Remove costs
         *
         * @param \AppBundle\Entity\Cost $costs
         */
        public function removeCost(\AppBundle\Entity\Cost $costs)
        {
            $this->costs->removeElement($costs);
        }

        /**
         * Get costs
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getCosts()
        {
            return $this->costs;
        }

        /**
         * Set image
         *
         * @param \AppBundle\Entity\File $image
         *
         * @return Commission
         */
        public function setImage( File $image = null ){
            $this->image = $image;

            return $this;
        }

        /**
         * Get image
         * @return \AppBundle\Entity\File
         */
        public function getImage(){
            return $this->image;
        }

        /**
         * Set clientApproved
         *
         * @param boolean $clientApproved
         * @return Commission
         */
        public function setClientApproved($clientApproved)
        {
            $this->clientApproved = $clientApproved;

            return $this;
        }

        /**
         * Get clientApproved
         *
         * @return boolean
         */
        public function getClientApproved()
        {
            return $this->clientApproved;
        }

        /**
         * Set billable
         *
         * @param boolean $billable
         * @return Commission
         */
        public function setBillable($billable)
        {
            $this->billable = $billable;

            return $this;
        }

        /**
         * Get billable
         *
         * @return boolean
         */
        public function getBillable()
        {
            return $this->billable;
        }

        /**
         * Set enabled
         *
         * @param boolean $enabled
         * @return Commission
         */
        public function setEnabled($enabled)
        {
            $this->enabled = $enabled;

            return $this;
        }

        /**
         * Get enabled
         *
         * @return boolean
         */
        public function getEnabled()
        {
            return $this->enabled;
        }

        /**
         * Set closed
         *
         * @param boolean $closed
         * @return Commission
         */
        public function setClosed($closed)
        {
            $this->closed = $closed;

            return $this;
        }

        /**
         * Get closed
         *
         * @return boolean
         */
        public function getClosed()
        {
            return $this->closed;
        }

        /**
         * Set campaign
         *
         * @param \AppBundle\Entity\Campaign $campaign
         * @return Commission
         */
        public function setCampaign(\AppBundle\Entity\Campaign $campaign = null)
        {
            $this->campaign = $campaign;

            return $this;
        }

        /**
         * Get campaign
         *
         * @return \AppBundle\Entity\Campaign
         */
        public function getCampaign()
        {
            return $this->campaign;
        }

        /**
         * Set contactPersonList
         *
         * @param \AppBundle\Entity\ContactPersonList $contactPersonList
         * @return Commission
         */
        public function setContactPersonList(\AppBundle\Entity\ContactPersonList $contactPersonList = null)
        {
            $this->contactPersonList = $contactPersonList;

            return $this;
        }

        /**
         * Get contactPersonList
         *
         * @return \AppBundle\Entity\ContactPersonList
         */
        public function getContactPersonList()
        {
            return $this->contactPersonList;
        }

        /**
         * Set sourceList
         *
         * @param \AppBundle\Entity\SourceList $sourceList
         * @return Commission
         */
        public function setSourceList(\AppBundle\Entity\SourceList $sourceList = null)
        {
            $this->sourceList = $sourceList;

            return $this;
        }

        /**
         * Get sourceList
         *
         * @return \AppBundle\Entity\SourceList
         */
        public function getSourceList()
        {
            return $this->sourceList;
        }

        /**
         * Set startDate
         *
         * @param \AppBundle\Entity\YearMonth $startDate
         * @return Commission
         */
        public function setStartDate(\AppBundle\Entity\YearMonth $startDate = null)
        {
            $this->startDate = $startDate;

            return $this;
        }

        /**
         * Get startDate
         *
         * @return \AppBundle\Entity\YearMonth
         */
        public function getStartDate()
        {
            return $this->startDate;
        }

        /**
         * Set endDate
         *
         * @param \AppBundle\Entity\YearMonth $endDate
         * @return Commission
         */
        public function setEndDate(\AppBundle\Entity\YearMonth $endDate = null)
        {
            $this->endDate = $endDate;

            return $this;
        }

        /**
         * Get endDate
         *
         * @return \AppBundle\Entity\YearMonth
         */
        public function getEndDate()
        {
            return $this->endDate;
        }

        /**
         * Add allocationContainers
         *
         * @param \AppBundle\Entity\AllocationContainer $allocationContainers
         * @return Commission
         */
        public function addAllocationContainer(\AppBundle\Entity\AllocationContainer $allocationContainers)
        {
            $allocationContainers->setCommission($this);
            $this->allocationContainers[] = $allocationContainers;

            return $this;
        }

        /**
         * Remove allocationContainers
         *
         * @param \AppBundle\Entity\AllocationContainer $allocationContainers
         */
        public function removeAllocationContainer(\AppBundle\Entity\AllocationContainer $allocationContainers)
        {
            $allocationContainers->setCommission(null);
            $this->allocationContainers->removeElement($allocationContainers);
        }

        /**
         * Get allocationContainers
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getAllocationContainers()
        {
            return $this->allocationContainers;
        }

        /**
         * Set name
         *
         * @param string $name
         * @return Commission
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
         * @return boolean
         */
        public function isFeeAble(){
            return $this->feeAble;
        }

        /**
         * @param boolean $feeAble
         */
        public function setFeeAble( $feeAble ){
            $this->feeAble = $feeAble;
        }


        /**
         * Set repeatable
         *
         * @param boolean $repeatable
         * @return Commission
         */
        public function setRepeatable($repeatable)
        {
            $this->repeatable = $repeatable;

            return $this;
        }

        /**
         * Get repeatable
         *
         * @return boolean
         */
        public function getRepeatable()
        {
            return $this->repeatable;
        }

        /**
         * Set nameOwn
         *
         * @param string $nameOwn
         * @return Commission
         */
        public function setNameOwn($nameOwn)
        {
            $this->nameOwn = $nameOwn;

            return $this;
        }

        /**
         * Get nameOwn
         *
         * @return string
         */
        public function getNameOwn()
        {
            return $this->nameOwn;
        }

        /**
         * Add allocationUnits
         *
         * @param \AppBundle\Entity\AllocationUnit $allocationUnits
         * @return Commission
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

        /**
         * Set description
         *
         * @param string $description
         * @return Commission
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
         * Set status
         *
         * @param string $status
         * @return Commission
         */
        public function setStatus($status)
        {
            $this->status = $status;

            return $this;
        }

        /**
         * Get status
         *
         * @return string
         */
        public function getStatus()
        {
            return $this->status;
        }

        /**
         * Add invoiceItems
         *
         * @param \AppBundle\Entity\InvoiceItem $invoiceItems
         * @return Commission
         */
        public function addInvoiceItem(\AppBundle\Entity\InvoiceItem $invoiceItems)
        {
            $this->invoiceItems[] = $invoiceItems;

            return $this;
        }

        /**
         * Remove invoiceItems
         *
         * @param \AppBundle\Entity\InvoiceItem $invoiceItems
         */
        public function removeInvoiceItem(\AppBundle\Entity\InvoiceItem $invoiceItems)
        {
            $this->invoiceItems->removeElement($invoiceItems);
        }

        /**
         * Get invoiceItems
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getInvoiceItems()
        {
            return $this->invoiceItems;
        }
    }
