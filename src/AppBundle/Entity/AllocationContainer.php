<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * AllocationContainer
 *
 * @ORM\Table(name="AllocationContainer")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\AllocationContainerRepository")
 */
class AllocationContainer
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
     * @ORM\Column(name="version", type="integer", nullable=true)
     */
    private $version;

    /**
     * @var Commission
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Commission", inversedBy="allocationContainers", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $commission;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled = true;

    /**
     * @var bool
     *
     * @ORM\Column(name="client_approved", type="boolean")
     */
    private $clientApproved = false;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\AllocationContainerList", mappedBy="allocationContainer",
     *                                                                         cascade={"persist"})
     */
    private $allocationContainerLists;

    public function __clone(){
        if($this->id){
            $this->id = null;
            $collectionCopy = new ArrayCollection();
            /** @var AllocationContainerList $allocationContainerList */
            foreach($this->getAllocationContainerLists() as $allocationContainerList){
                /** @var AllocationContainerList $allocationContainerListCopy */
                $allocationContainerListCopy = clone $allocationContainerList;
                $collectionCopy->add($allocationContainerListCopy);
                $allocationContainerListCopy->setAllocationContainer($this);
            }
            $this->allocationContainerLists = $collectionCopy;
        }
    }

    public function getSums(){

        $array = [];

        /** @var AllocationContainerList $allocationContainerList */
        foreach($this->getAllocationContainerLists() as $allocationContainerList){
            $array[] = $allocationContainerList->getSums();
        }

        $sums = new AllocationsContainerSums();
        $sums->sumarize($array);

        return $sums;

    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->allocationContainerLists = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set commission
     *
     * @param \AppBundle\Entity\Commission $commission
     * @return AllocationContainer
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
     * Add allocationContainerLists
     *
     * @param \AppBundle\Entity\AllocationContainerList $allocationContainerLists
     * @return AllocationContainer
     */
    public function addAllocationContainerList(\AppBundle\Entity\AllocationContainerList $allocationContainerLists)
    {
        $allocationContainerLists->setAllocationContainer($this);
        $this->allocationContainerLists[] = $allocationContainerLists;

        return $this;
    }

    /**
     * Remove allocationContainerLists
     *
     * @param \AppBundle\Entity\AllocationContainerList $allocationContainerLists
     */
    public function removeAllocationContainerList(\AppBundle\Entity\AllocationContainerList $allocationContainerLists)
    {
        $this->allocationContainerLists->removeElement($allocationContainerLists);
    }

    /**
     * Get allocationContainerLists
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAllocationContainerLists()
    {
        return $this->allocationContainerLists;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return AllocationContainer
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
     * Set clientApproved
     *
     * @param boolean $clientApproved
     * @return AllocationContainer
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
     * Set version
     *
     * @param integer $version
     * @return AllocationContainer
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
}
