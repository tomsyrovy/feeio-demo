<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SubcommissionTeamUserCompany
 *
 * @ORM\Table(name="SubcommissionTeamUserCompany")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\SubcommissionTeamUserCompanyRepository")
 */
class SubcommissionTeamUserCompany
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
     * @var UserCompany
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserCompany", inversedBy="subcommissionTeamUserCompanyRelations")
     */
    private $userCompany;

    /**
     * @var SubcommissionTeam
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SubcommissionTeam", inversedBy="members", cascade={"persist", "remove"})
     */
    private $subcommissionTeam;

    /**
     * @var float
     *
     * @ORM\Column(name="rateInternal", type="decimal", nullable=true)
     */
    private $rateInternal = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="rateExternal", type="decimal", nullable=true)
     */
    private $rateExternal = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="hours", type="decimal", nullable=true)
     */
    private $hours = 0;


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
     * Set rateInternal
     *
     * @param float $rateInternal
     * @return SubcommissionTeamUserCompany
     */
    public function setRateInternal($rateInternal)
    {
        $this->rateInternal = $rateInternal;

        return $this;
    }

    /**
     * Get rateInternal
     *
     * @return float
     */
    public function getRateInternal()
    {
        return $this->rateInternal;
    }


    /**
     * Set rateExternal
     *
     * @param float $rateExternal
     * @return SubcommissionTeamUserCompany
     */
    public function setRateExternal($rateExternal)
    {
        $this->rateExternal = $rateExternal;

        return $this;
    }

    /**
     * Get rateExternal
     *
     * @return float
     */
    public function getRateExternal()
    {
        return $this->rateExternal;
    }

    /**
     * Set hours
     *
     * @param float $hours
     * @return SubcommissionTeamUserCompany
     */
    public function setHours($hours)
    {
        $this->hours = $hours;

        return $this;
    }

    /**
     * Get hours
     *
     * @return float
     */
    public function getHours()
    {
        return $this->hours;
    }

    /**
     * Set userCompany
     *
     * @param \AppBundle\Entity\UserCompany $userCompany
     * @return SubcommissionTeamUserCompany
     */
    public function setUserCompany(\AppBundle\Entity\UserCompany $userCompany = null)
    {
        $this->userCompany = $userCompany;

        return $this;
    }

    /**
     * Get userCompany
     *
     * @return \AppBundle\Entity\UserCompany 
     */
    public function getUserCompany()
    {
        return $this->userCompany;
    }

    /**
     * Set subcommissionTeam
     *
     * @param \AppBundle\Entity\SubcommissionTeam $subcommissionTeam
     * @return SubcommissionTeamUserCompany
     */
    public function setSubcommissionTeam(\AppBundle\Entity\SubcommissionTeam $subcommissionTeam = null)
    {
        $this->subcommissionTeam = $subcommissionTeam;

        return $this;
    }

    /**
     * Get subcommissionTeam
     *
     * @return \AppBundle\Entity\SubcommissionTeam 
     */
    public function getSubcommissionTeam()
    {
        return $this->subcommissionTeam;
    }
}
