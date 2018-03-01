<?php

namespace AppBundle\Entity;

use AppBundle\DataObject\SubcommissionTeamDataDataObject;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\QueryBuilder;

/**
 * SubcommissionTeam
 *
 * @ORM\Table(name="SubcommissionTeam")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\SubcommissionTeamRepository")
 */
class SubcommissionTeam
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
     * @var \DateTime
     *
     * @ORM\Column(name="dateFrom", type="datetime")
     */
    private $dateFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateUntil", type="datetime", nullable=true)
     */
    private $dateUntil;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\SubcommissionTeamUserCompany", mappedBy="subcommissionTeam", cascade={"persist", "remove"})
     */
    private $members;

    /**
     * @var Subcommission
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Subcommission", inversedBy="subcommissionTeams")
     */
    private $subcommission;


    public function __clone() {

        if($this->getId()){

            // cloning the relation M which is a OneToMany
            $mClone = new ArrayCollection();

            foreach ($this->getMembers() as $item) {
                $itemClone = clone($item);
                $itemClone->setSubcommissionTeam($this);
                $mClone->add($itemClone);
            }

            $this->members = $mClone;

        }
    }

    /**
     * @return \AppBundle\DataObject\SubcommissionTeamDataDataObject
     */
    public function getData(){

        $subcommissionTeamDataDO = new SubcommissionTeamDataDataObject();
        $subcommissionTeamDataDO->processSumOfSubcommissionTeam($this);

        return $subcommissionTeamDataDO;

    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->members = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set dateFrom
     *
     * @param \DateTime $dateFrom
     * @return SubcommissionTeam
     */
    public function setDateFrom($dateFrom)
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    /**
     * Get dateFrom
     *
     * @return \DateTime 
     */
    public function getDateFrom()
    {
        return $this->dateFrom;
    }

    /**
     * Set dateUntil
     *
     * @param \DateTime $dateUntil
     * @return SubcommissionTeam
     */
    public function setDateUntil($dateUntil)
    {
        $this->dateUntil = $dateUntil;

        return $this;
    }

    /**
     * Get dateUntil
     *
     * @return \DateTime 
     */
    public function getDateUntil()
    {
        return $this->dateUntil;
    }

    /**
     * Add members
     *
     * @param \AppBundle\Entity\SubcommissionTeamUserCompany $members
     * @return SubcommissionTeam
     */
    public function addMember(\AppBundle\Entity\SubcommissionTeamUserCompany $members)
    {
        $this->members[] = $members;
        $members->setSubcommissionTeam($this);

        return $this;
    }

    /**
     * Remove members
     *
     * @param \AppBundle\Entity\SubcommissionTeamUserCompany $members
     */
    public function removeMember(\AppBundle\Entity\SubcommissionTeamUserCompany $members)
    {
        $this->members->removeElement($members);
    }

    /**
     * Get members
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * Set subcommission
     *
     * @param \AppBundle\Entity\Subcommission $subcommission
     * @return SubcommissionTeam
     */
    public function setSubcommission(\AppBundle\Entity\Subcommission $subcommission = null)
    {
        $this->subcommission = $subcommission;

        return $this;
    }

    /**
     * Get subcommission
     *
     * @return \AppBundle\Entity\Subcommission 
     */
    public function getSubcommission()
    {
        return $this->subcommission;
    }
}
