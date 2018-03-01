<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Campaign
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\CampaignRepository")
 */
class Campaign extends AbstractCommissionHierarchy
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
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Client", inversedBy="campaigns", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $client;

    /**
     * @var CompanyGroup
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CompanyGroup", inversedBy="campaigns")
     */
    private $companyGroup;

    /**
     * @var Year
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Year", inversedBy="campaigns")
     */
    private $year;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Commission", mappedBy="campaign")
     */
    private $commissions;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CampaignManager", mappedBy="campaign", cascade={"persist"})
     */
    private $campaignManagers;

    /**
     * @var string
     *
     * @ORM\Column(name="name_own", type="string", length=255)
     */
    private $nameOwn;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    public function getSums(){

        $array = [];

        /** @var Commission $item */
        foreach($this->getCommissions() as $item){
            if($item->getEnabled()){
                $array[] = $item->getSums();
            }
        }

        $sums = new AllocationsContainerSums();
        $sums->sumarize($array);

        return $sums;

    }

    /**
     * @return string
     */
    public function generateName(){
        $i = 1;
        /** @var Campaign $campaign */
        foreach($this->client->getCampaigns() as $campaign){
            if($campaign === $this){
                break;
            }
            if($campaign->getYear() === $this->getYear()){
                $i++;
            }
        }
        $i = str_pad($i, 2, '0', STR_PAD_LEFT);
        return $this->client->getCode().substr($this->year->getYear(), -2).$i;
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
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     * @return Campaign
     */
    public function setClient(\AppBundle\Entity\Client $client = null)
    {
        $client->addCampaign($this);
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \AppBundle\Entity\Client 
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set companyGroup
     *
     * @param \AppBundle\Entity\CompanyGroup $companyGroup
     * @return Campaign
     */
    public function setCompanyGroup(\AppBundle\Entity\CompanyGroup $companyGroup = null)
    {
        $this->companyGroup = $companyGroup;

        return $this;
    }

    /**
     * Get companyGroup
     *
     * @return \AppBundle\Entity\CompanyGroup 
     */
    public function getCompanyGroup()
    {
        return $this->companyGroup;
    }

    /**
     * Set year
     *
     * @param \AppBundle\Entity\Year $year
     * @return Campaign
     */
    public function setYear(\AppBundle\Entity\Year $year = null)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return \AppBundle\Entity\Year 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Campaign
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
     * @return Campaign
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
     * Set contactPersonList
     *
     * @param \AppBundle\Entity\ContactPersonList $contactPersonList
     * @return Campaign
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
     * @return Campaign
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
     * Constructor
     */
    public function __construct()
    {
        $this->commissions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add commissions
     *
     * @param \AppBundle\Entity\Commission $commissions
     * @return Campaign
     */
    public function addCommission(\AppBundle\Entity\Commission $commissions)
    {
        $this->commissions[] = $commissions;

        return $this;
    }

    /**
     * Remove commissions
     *
     * @param \AppBundle\Entity\Commission $commissions
     */
    public function removeCommission(\AppBundle\Entity\Commission $commissions)
    {
        $this->commissions->removeElement($commissions);
    }

    /**
     * Get commissions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCommissions()
    {
        return $this->commissions;
    }

    /**
     * Add campaignManagers
     *
     * @param \AppBundle\Entity\CampaignManager $campaignManagers
     * @return Campaign
     */
    public function addCampaignManager(\AppBundle\Entity\CampaignManager $campaignManagers)
    {
        $campaignManagers->setCampaign($this);
        $this->campaignManagers[] = $campaignManagers;

        return $this;
    }

    /**
     * Remove campaignManagers
     *
     * @param \AppBundle\Entity\CampaignManager $campaignManagers
     */
    public function removeCampaignManager(\AppBundle\Entity\CampaignManager $campaignManagers)
    {
        $this->campaignManagers->removeElement($campaignManagers);
    }

    /**
     * Get campaignManagers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCampaignManagers()
    {
        return $this->campaignManagers;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Campaign
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
     * Set nameOwn
     *
     * @param string $nameOwn
     * @return Campaign
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
}
