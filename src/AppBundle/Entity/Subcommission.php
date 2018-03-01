<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Interfaces\RemoveableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Subcommission
 *
 * @ORM\Table(name="Subcommission")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\SubcommissionRepository")
 */
class Subcommission implements RemoveableInterface
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Commission", inversedBy="subcommissions")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $commission;

    /**
     * @var YearMonth
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\YearMonth", inversedBy="subcommissions")
     */
    private $yearmonth;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\SubcommissionTemporality", mappedBy="subcommission", cascade={"persist", "remove"})
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $temporalities;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\SubcommissionTeam", mappedBy="subcommission", cascade={"persist", "remove"})
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $subcommissionTeams;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $enabled = true;


    public function getCode(){

        return $this->getCommission()->getName().$this->getYearmonth()->getShortCode();

    }

    public function getData(){

        return $this->getTemporalities()->first();

    }

    public function getTeamData(){

        return $this->getSubcommissionTeams()->first();

    }

    public function canRemove(){

        return true; //Subzakázka může být odstraněna vždy.

    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->fieldValues = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Subcommission
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
     * Set yearmonth
     *
     * @param \AppBundle\Entity\YearMonth $yearmonth
     * @return Subcommission
     */
    public function setYearmonth(\AppBundle\Entity\YearMonth $yearmonth = null)
    {
        $this->yearmonth = $yearmonth;

        return $this;
    }

    /**
     * Get yearmonth
     *
     * @return \AppBundle\Entity\YearMonth 
     */
    public function getYearmonth()
    {
        return $this->yearmonth;
    }

    /**
     * Add temporalities
     *
     * @param \AppBundle\Entity\SubcommissionTemporality $temporalities
     * @return Subcommission
     */
    public function addTemporality(\AppBundle\Entity\SubcommissionTemporality $temporalities)
    {
        $this->temporalities[] = $temporalities;

        return $this;
    }

    /**
     * Remove temporalities
     *
     * @param \AppBundle\Entity\SubcommissionTemporality $temporalities
     */
    public function removeTemporality(\AppBundle\Entity\SubcommissionTemporality $temporalities)
    {
        $this->temporalities->removeElement($temporalities);
    }

    /**
     * Get temporalities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTemporalities()
    {
        return $this->temporalities;
    }

    /**
     * Add subcommissionTeams
     *
     * @param \AppBundle\Entity\SubcommissionTeam $subcommissionTeams
     * @return Subcommission
     */
    public function addSubcommissionTeam(\AppBundle\Entity\SubcommissionTeam $subcommissionTeams)
    {
        $this->subcommissionTeams[] = $subcommissionTeams;

        return $this;
    }

    /**
     * Remove subcommissionTeams
     *
     * @param \AppBundle\Entity\SubcommissionTeam $subcommissionTeams
     */
    public function removeSubcommissionTeam(\AppBundle\Entity\SubcommissionTeam $subcommissionTeams)
    {
        $this->subcommissionTeams->removeElement($subcommissionTeams);
    }

    /**
     * Get subcommissionTeams
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSubcommissionTeams()
    {
        return $this->subcommissionTeams;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Subcommission
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
}
