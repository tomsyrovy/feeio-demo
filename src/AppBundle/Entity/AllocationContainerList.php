<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * AllocationContainerList
 *
 * @ORM\Table(name="AllocationContainerList")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\AllocationContainerListRepository")
 */
class AllocationContainerList
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
     * @var AllocationContainer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AllocationContainer", inversedBy="allocationContainerLists", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $allocationContainer;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\AllocationContainerListItem", mappedBy="allocationContainerList",
     *                                                                             cascade={"persist"})
     */
    private $allocationContainerListItems;

    public function __clone(){
        if($this->id){
            $this->id = null;
            $collectionCopy = new ArrayCollection();
            /** @var AllocationContainerListItem $allocationContainerListItem */
            foreach($this->getAllocationContainerListItems() as $allocationContainerListItem){
                /** @var AllocationContainerListItem $allocationContainerListItemCopy */
                $allocationContainerListItemCopy = clone $allocationContainerListItem;
                $collectionCopy->add($allocationContainerListItemCopy);
                $allocationContainerListItemCopy->setAllocationContainerList($this);
            }
            $this->allocationContainerListItems = $collectionCopy;
        }
    }

    public function getSums(){

        $sums = new AllocationsContainerSums();
        $sums->sumarize($this->getAllocationContainerListItems());

        return $sums;

    }

//    public function getSums2(){
//
//        $commission = $this->getAllocationContainer()->getCommission();
//        $allocationContainers_All = array_filter($commission->getAllocationContainers()->toArray(), function($entry){
//            return $entry->getEnabled();
//        });
//        $allocationContainers_Master = array_filter($allocationContainers_All, function($entry){
//            return $entry->getClientApproved() && !$entry->getYearmonth();
//        });
//        $allocationContainers_Allocation = array_filter($allocationContainers_All, function($entry){
//            return $entry->getYearmonth();
//        });
//
//        $items_Master = $this->getItems($allocationContainers_Master);
//        $items_Allocation = $this->getItems($allocationContainers_Allocation);
//
//        $items = $items_Allocation;
//
//        /** @var AllocationContainerListItem $item_Master */
//        foreach($items_Master as $item_Master){
//            if(!$item_Master->getExternalSource()){
//                $isInMaster = false;
//                /** @var AllocationContainerListItem $item */
//                foreach($items as $item){
//                    if($item_Master->getGeneralSource() === $item->getGeneralSource()){
//                        $isInMaster = true;
//                        break 1;
//                    }
//                }
//                if(!$isInMaster){
//                    $items[] = $item_Master;
//                }
//            }else{
//                $items[] = $item_Master;
//            }
//        }
//
////        dump($items_Master);
////        dump($items_Allocation);
////        dump($items);
////        exit;
//
//        $sums = new AllocationsContainerSums();
//        $sums->sumarize($items);
//
////        dump($sums);exit;
//
//        return $sums;
//
//    }



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
     * @return AllocationContainerList
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
     * Set allocationContainer
     *
     * @param \AppBundle\Entity\AllocationContainer $allocationContainer
     * @return AllocationContainerList
     */
    public function setAllocationContainer(\AppBundle\Entity\AllocationContainer $allocationContainer = null)
    {
        $this->allocationContainer = $allocationContainer;

        return $this;
    }

    /**
     * Get allocationContainer
     *
     * @return \AppBundle\Entity\AllocationContainer 
     */
    public function getAllocationContainer()
    {
        return $this->allocationContainer;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->allocationContainerListItems = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add allocationContainerListItems
     *
     * @param \AppBundle\Entity\AllocationContainerListItem $allocationContainerListItems
     * @return AllocationContainerList
     */
    public function addAllocationContainerListItem(\AppBundle\Entity\AllocationContainerListItem $allocationContainerListItems)
    {
        $allocationContainerListItems->setAllocationContainerList($this);
        $this->allocationContainerListItems[] = $allocationContainerListItems;

        return $this;
    }

    /**
     * Remove allocationContainerListItems
     *
     * @param \AppBundle\Entity\AllocationContainerListItem $allocationContainerListItems
     */
    public function removeAllocationContainerListItem(\AppBundle\Entity\AllocationContainerListItem $allocationContainerListItems)
    {
        $this->allocationContainerListItems->removeElement($allocationContainerListItems);
    }

    /**
     * Get allocationContainerListItems
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAllocationContainerListItems()
    {
        return $this->allocationContainerListItems;
    }
}
